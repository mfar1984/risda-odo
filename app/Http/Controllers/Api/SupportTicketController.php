<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    /**
     * Get all tickets for authenticated user (driver/staff)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get tickets based on user access
        $query = SupportTicket::with(['creator', 'assignedAdmin', 'messages'])
                              ->where(function ($q) use ($user) {
                                  // User's own tickets
                                  $q->where('created_by', $user->id)
                                    // Or assigned to user
                                    ->orWhere('assigned_to', $user->id)
                                    // Or user is participant
                                    ->orWhereHas('participants', function($pq) use ($user) {
                                        $pq->where('user_id', $user->id);
                                    })
                                    // Or unassigned Android tickets in same organization
                                    ->orWhere(function ($orgQ) use ($user) {
                                        $orgQ->where('jenis_organisasi', $user->jenis_organisasi)
                                             ->where('organisasi_id', $user->organisasi_id)
                                             ->where('source', 'android')
                                             ->whereNull('assigned_to');
                                    });
                              })
                              ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->get()->map(function ($ticket) {
            return $this->formatTicketData($ticket);
        });

        return response()->json([
            'success' => true,
            'data' => $tickets,
            'meta' => [
                'total' => $tickets->count(),
                'filter' => $request->status ?? 'all',
            ]
        ], 200);
    }

    /**
     * Get single ticket details with messages
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::with(['creator', 'assignedAdmin', 'messages.user', 'participants'])
                               ->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        // Check access
        if (!$ticket->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan',
            ], 403);
        }

        // Auto-assign Android ticket to first viewer
        if ($ticket->source === 'android' && 
            $ticket->assigned_to === null && 
            $user->jenis_organisasi !== 'semua') {
            
            $ticket->update(['assigned_to' => $user->id]);
            
            // Log activity
            activity('support')
                ->performedOn($ticket)
                ->causedBy($user)
                ->withProperties([
                    'ticket_number' => $ticket->ticket_number,
                    'assigned_to' => $user->name,
                    'ip' => $request->ip(),
                ])
                ->event('auto_assigned')
                ->log('Tiket Android auto-assigned kepada pemandu');
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatTicketDetailData($ticket),
        ], 200);
    }

    /**
     * Create new ticket from Android app
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:teknikal,akaun,perjalanan,tuntutan,lain',
            'priority' => 'required|in:rendah,sederhana,tinggi,kritikal',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = $path;
            }
        }

        // Create ticket
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'baru',
            'jenis_organisasi' => $user->jenis_organisasi,
            'organisasi_id' => $user->organisasi_id,
            'created_by' => $user->id,
            'assigned_to' => null, // Android tickets start unassigned
            'source' => 'android',
            'ip_address' => $request->ip(),
            'device' => $request->header('User-Agent'),
            'platform' => 'android',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Create initial message
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'role' => 'pengguna',
            'message' => $request->message,
            'attachments' => $attachments,
            'ip_address' => $request->ip(),
            'location' => ($request->latitude && $request->longitude) 
                ? "{$request->latitude}, {$request->longitude}" 
                : null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $ticket->update(['last_reply_at' => now()]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($user)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'source' => 'android',
                'ip' => $request->ip(),
            ])
            ->event('created')
            ->log('Tiket sokongan Android dicipta');

        // Notify staff in same organization
        if ($user->jenis_organisasi !== 'semua') {
            $staffUsers = User::where('jenis_organisasi', $user->jenis_organisasi)
                             ->where('organisasi_id', $user->organisasi_id)
                             ->where('id', '!=', $user->id)
                             ->pluck('id')
                             ->toArray();
            
            if (!empty($staffUsers)) {
                foreach ($staffUsers as $staffId) {
                    Notification::create([
                        'user_id' => $staffId,
                        'type' => 'support_ticket',
                        'title' => 'Tiket Baru dari Pemandu',
                        'message' => "{$user->name}: {$ticket->subject}",
                        'action_url' => route('help.hubungi-sokongan'),
                        'data' => ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number],
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya dicipta',
            'data' => $this->formatTicketData($ticket->load(['creator', 'messages'])),
        ], 201);
    }

    /**
     * Send message/reply to ticket
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        // Check access
        if (!$ticket->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan',
            ], 403);
        }

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = $path;
            }
        }

        // Create message
        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'role' => 'pengguna',
            'message' => $request->message,
            'attachments' => $attachments,
            'ip_address' => $request->ip(),
            'location' => ($request->latitude && $request->longitude) 
                ? "{$request->latitude}, {$request->longitude}" 
                : null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $ticket->update([
            'status' => 'dalam_proses',
            'last_reply_at' => now(),
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($user)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'message_preview' => substr($request->message, 0, 100),
                'has_attachments' => !empty($attachments),
                'source' => 'android',
                'ip' => $request->ip(),
            ])
            ->event('replied')
            ->log('Balasan baru dari Android');

        // Notify relevant users (assigned, participants, admins)
        $this->notifyTicketReply($ticket, $user, $request->message);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berjaya dihantar',
            'data' => $this->formatMessageData($message),
        ], 201);
    }

    /**
     * Get messages for a ticket (for real-time sync)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::with('messages.user')->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        if (!$ticket->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan',
            ], 403);
        }

        $messages = $ticket->messages->map(function ($msg) {
            return $this->formatMessageData($msg);
        });

        return response()->json([
            'success' => true,
            'data' => $messages,
            'meta' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'total_messages' => $messages->count(),
            ]
        ], 200);
    }

    /**
     * Delete ticket (driver can delete their own tickets if not yet processed)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        // Only creator can delete, and only if status is 'baru'
        if ($ticket->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya creator boleh memadam tiket',
            ], 403);
        }

        if ($ticket->status !== 'baru') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket yang sudah diproses tidak boleh dipadam',
            ], 400);
        }

        // Log before delete
        activity('support')
            ->performedOn($ticket)
            ->causedBy($user)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'source' => 'android',
                'ip' => $request->ip(),
            ])
            ->event('deleted')
            ->log('Tiket Android dipadam oleh creator');

        // Delete messages first
        $ticket->messages()->delete();
        
        // Delete ticket
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya dipadam',
        ], 200);
    }

    /**
     * Upload attachment to existing ticket
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        if (!$ticket->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan',
            ], 403);
        }

        // Upload file
        $path = $request->file('file')->store('support-attachments', 'public');
        $fileName = $request->file('file')->getClientOriginalName();

        return response()->json([
            'success' => true,
            'message' => 'Fail berjaya dimuat naik',
            'data' => [
                'path' => $path,
                'file_name' => $fileName,
                'url' => asset('storage/' . $path),
            ],
        ], 201);
    }

    /**
     * Download attachment
     * 
     * @param Request $request
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment(Request $request, $path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Fail tidak dijumpai',
            ], 404);
        }

        return response()->download($fullPath);
    }

    /**
     * Get ticket status only
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        if (!$ticket->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak dibenarkan',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->status,
                'status_label' => $ticket->status_label,
                'last_reply_at' => $ticket->last_reply_at ? $ticket->last_reply_at->toIso8601String() : null,
                'message_count' => $ticket->messages->count(),
                'unread_count' => 0, // TODO: Implement unread tracking
            ],
        ], 200);
    }

    /**
     * Update typing status
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTypingStatus(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket || !$ticket->canBeAccessedBy($user)) {
            return response()->json(['success' => false], 403);
        }

        // Store typing status in cache (expires after 5 seconds)
        $cacheKey = "ticket_{$id}_typing_{$user->id}";
        \Cache::put($cacheKey, [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toIso8601String(),
        ], 5); // 5 seconds TTL

        return response()->json(['success' => true]);
    }

    /**
     * Get typing status for ticket
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypingStatus(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket || !$ticket->canBeAccessedBy($user)) {
            return response()->json(['success' => false], 403);
        }

        // Get all typing users (except current user)
        $typingUsers = [];
        $pattern = "ticket_{$id}_typing_*";
        
        // Get all cache keys for this ticket
        $allKeys = \Cache::get("ticket_{$id}_typing_users", []);
        
        foreach ($allKeys as $userId) {
            if ($userId != $user->id) {
                $cacheKey = "ticket_{$id}_typing_{$userId}";
                $typingData = \Cache::get($cacheKey);
                if ($typingData) {
                    $typingUsers[] = $typingData;
                }
            }
        }

        return response()->json([
            'success' => true,
            'typing_users' => $typingUsers,
        ]);
    }

    /**
     * Reopen closed ticket (driver can request reopening)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reopen(Request $request, $id)
    {
        $user = $request->user();
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak dijumpai',
            ], 404);
        }

        // Only creator can reopen
        if ($ticket->created_by !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya creator boleh membuka semula tiket',
            ], 403);
        }

        if ($ticket->status !== 'ditutup') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket belum ditutup',
            ], 400);
        }

        $ticket->update([
            'status' => 'dalam_proses',
            'closed_at' => null,
        ]);

        // Create system message
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'role' => 'sistem',
            'message' => "Tiket dibuka semula oleh {$user->name} (Android)",
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($user)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'source' => 'android',
                'ip' => $request->ip(),
            ])
            ->event('reopened')
            ->log('Tiket dibuka semula dari Android');

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya dibuka semula',
            'data' => $this->formatTicketData($ticket->load(['creator', 'messages'])),
        ], 200);
    }

    /**
     * Format ticket data for API response
     */
    private function formatTicketData($ticket)
    {
        $user = auth()->user();
        
        // Count unread messages for this user
        $unreadCount = $ticket->messages->filter(function($msg) use ($user) {
            return $msg->user_id !== $user->id && !$msg->isReadBy($user->id);
        })->count();
        
        return [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
            'priority_label' => $ticket->priority_label,
            'status' => $ticket->status,
            'status_label' => $ticket->status_label,
            'source' => $ticket->source,
            'created_at' => $ticket->created_at->toIso8601String(),
            'updated_at' => $ticket->updated_at->toIso8601String(),
            'last_reply_at' => $ticket->last_reply_at ? $ticket->last_reply_at->toIso8601String() : null,
            'message_count' => $ticket->messages->count(),
            'unread_count' => $unreadCount,
            'creator' => [
                'id' => $ticket->creator->id,
                'name' => $ticket->creator->name,
                'email' => $ticket->creator->email,
            ],
            'assigned_to' => $ticket->assignedAdmin ? [
                'id' => $ticket->assignedAdmin->id,
                'name' => $ticket->assignedAdmin->name,
                'email' => $ticket->assignedAdmin->email,
            ] : null,
        ];
    }

    /**
     * Format ticket detail data (with messages)
     */
    private function formatTicketDetailData($ticket)
    {
        $data = $this->formatTicketData($ticket);
        
        $data['messages'] = $ticket->messages->map(function ($msg) {
            return $this->formatMessageData($msg);
        });

        $data['participants'] = $ticket->participants->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'email' => $p->email,
            ];
        });

        return $data;
    }

    /**
     * Format message data for API response
     */
    private function formatMessageData($message)
    {
        return [
            'id' => $message->id,
            'ticket_id' => $message->ticket_id,
            'message' => $message->message,
            'role' => $message->role,
            'attachments' => $message->attachments ?? [],
            'ip_address' => $message->ip_address,
            'location' => $message->location,
            'latitude' => $message->latitude,
            'longitude' => $message->longitude,
            'created_at' => $message->created_at->toIso8601String(),
            'user' => $message->user ? [
                'id' => $message->user->id,
                'name' => $message->user->name,
            ] : null,
        ];
    }

    /**
     * Notify users about new reply
     */
    private function notifyTicketReply($ticket, $sender, $messagePreview)
    {
        // Get all staff in same organization + admins
        $notifyUsers = collect();
        
        // Staff in same organization
        if ($ticket->jenis_organisasi !== 'semua') {
            $staffUsers = User::where('jenis_organisasi', $ticket->jenis_organisasi)
                             ->where('organisasi_id', $ticket->organisasi_id)
                             ->where('id', '!=', $sender->id)
                             ->pluck('id');
            $notifyUsers = $notifyUsers->merge($staffUsers);
        }
        
        // All administrators
        $adminUsers = User::where('jenis_organisasi', 'semua')
                         ->where('id', '!=', $sender->id)
                         ->pluck('id');
        $notifyUsers = $notifyUsers->merge($adminUsers);
        
        // Assigned person & participants
        if ($ticket->assigned_to && $ticket->assigned_to !== $sender->id) {
            $notifyUsers->push($ticket->assigned_to);
        }
        $notifyUsers = $notifyUsers->merge($ticket->participants->pluck('id'));
        
        // Remove duplicates
        $notifyUsers = $notifyUsers->unique()->filter()->values();

        foreach ($notifyUsers as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'support_reply',
                'title' => 'Balasan Baru - ' . $ticket->ticket_number,
                'message' => "{$sender->name}: " . substr($messagePreview, 0, 50) . '...',
                'action_url' => route('help.hubungi-sokongan'),
                'data' => ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number],
            ]);
        }
        
        // Send FCM push notification (if tokens exist)
        $this->sendFcmNotification($notifyUsers->toArray(), [
            'title' => 'Balasan Baru - ' . $ticket->ticket_number,
            'body' => "{$sender->name}: " . substr($messagePreview, 0, 80),
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }

    /**
     * Send FCM push notification
     */
    private function sendFcmNotification($userIds, $data)
    {
        if (empty($userIds)) return;

        $firebaseService = app(FirebaseService::class);
        
        // Send to each user
        foreach ($userIds as $userId) {
            $firebaseService->sendToUser(
                $userId,
                $data['title'] ?? 'New Message',
                $data['body'] ?? '',
                $data
            );
        }
    }

    /**
     * Get location from IP (same as web controller)
     */
    private function getLocationFromIP($ip)
    {
        if ($ip === '127.0.0.1' || $ip === 'localhost' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['location' => 'Local Network', 'latitude' => null, 'longitude' => null];
        }

        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");
            $data = json_decode($response, true);

            if ($data && $data['status'] === 'success') {
                $location = implode(', ', array_filter([
                    $data['city'] ?? null,
                    $data['regionName'] ?? null,
                    $data['country'] ?? null,
                ]));

                return [
                    'location' => $location ?: 'Unknown',
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            \Log::info("Failed to get location for IP {$ip}: " . $e->getMessage());
        }

        return ['location' => null, 'latitude' => null, 'longitude' => null];
    }

    /**
     * Get location name from GPS coordinates (reverse geocoding)
     */
    private function getLocationFromGPS($latitude, $longitude)
    {
        try {
            // Use Google Maps Geocoding API (or Nominatim for free)
            // Using Nominatim (OpenStreetMap) - free, no API key needed
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";
            
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: RISDA-Odometer-App/2.0\r\n"
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            if ($data && isset($data['address'])) {
                $address = $data['address'];
                $location = implode(', ', array_filter([
                    $address['suburb'] ?? $address['neighbourhood'] ?? $address['village'] ?? null,
                    $address['city'] ?? $address['town'] ?? null,
                    $address['state'] ?? null,
                ]));

                return $location ?: 'Unknown Location';
            }
        } catch (\Exception $e) {
            \Log::info("Failed to reverse geocode GPS {$latitude},{$longitude}: " . $e->getMessage());
        }

        return null;
    }
}
