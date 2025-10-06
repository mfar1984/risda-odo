<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of tickets (filtered by user role/scope)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $user = $currentUser; // For blade compatibility
        
        $query = SupportTicket::with(['creator', 'assignedAdmin', 'messages', 'participants']);

        // Multi-tenancy filtering based on NEW access control
        if ($currentUser->jenis_organisasi === 'semua') {
            // Administrator can see:
            // 1. Tickets assigned to them
            // 2. Tickets where they are participant
            // 3. Escalated tickets
            // 4. Tickets created by staff (NOT from Android/driver)
            // 5. Closed tickets
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_to', $currentUser->id)
                  ->orWhereHas('participants', function($pq) use ($currentUser) {
                      $pq->where('user_id', $currentUser->id);
                  })
                  ->orWhere('status', 'escalated')
                  ->orWhere(function ($subQ) {
                      $subQ->whereIn('jenis_organisasi', ['bahagian', 'stesen'])
                           ->where('source', '!=', 'android');
                  })
                  ->orWhere('status', 'ditutup');
            });
            
            // Calculate admin stats
            $adminStats = [
                'escalated' => SupportTicket::where('status', 'escalated')->count(),
                'staff' => SupportTicket::whereIn('jenis_organisasi', ['bahagian', 'stesen'])
                    ->where('source', '!=', 'android')
                    ->where('status', '!=', 'escalated')
                    ->where('status', '!=', 'ditutup')->count(),
                'today_resolved' => SupportTicket::where('status', 'ditutup')
                    ->whereDate('closed_at', today())->count(),
            ];
        } elseif ($currentUser->jenis_organisasi === 'bahagian') {
            // Bahagian staff can see tickets where they are:
            // 1. Creator
            // 2. Assigned to them
            // 3. Participant
            // 4. Android tickets in their organization (not yet assigned)
            $query->where(function ($q) use ($currentUser) {
                $q->where('created_by', $currentUser->id)
                  ->orWhere('assigned_to', $currentUser->id)
                  ->orWhereHas('participants', function($pq) use ($currentUser) {
                      $pq->where('user_id', $currentUser->id);
                  })
                  ->orWhere(function ($orgQ) use ($currentUser) {
                      $orgQ->where('jenis_organisasi', 'bahagian')
                           ->where('organisasi_id', $currentUser->organisasi_id)
                           ->where('source', 'android')
                           ->whereNull('assigned_to'); // Only unassigned Android tickets
                  });
            });
            
            $adminStats = null;
        } elseif ($currentUser->jenis_organisasi === 'stesen') {
            // Stesen staff can see tickets where they are:
            // 1. Creator
            // 2. Assigned to them
            // 3. Participant
            // 4. Android tickets in their organization (not yet assigned)
            $query->where(function ($q) use ($currentUser) {
                $q->where('created_by', $currentUser->id)
                  ->orWhere('assigned_to', $currentUser->id)
                  ->orWhereHas('participants', function($pq) use ($currentUser) {
                      $pq->where('user_id', $currentUser->id);
                  })
                  ->orWhere(function ($orgQ) use ($currentUser) {
                      $orgQ->where('jenis_organisasi', 'stesen')
                           ->where('organisasi_id', $currentUser->organisasi_id)
                           ->where('source', 'android')
                           ->whereNull('assigned_to'); // Only unassigned Android tickets
                  });
            });
            
            $adminStats = null;
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return view('help.hubungi-sokongan', compact('tickets', 'user', 'adminStats'));
    }

    /**
     * Show a single ticket thread
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::with(['creator', 'assignedAdmin', 'messages.user', 'participants'])->findOrFail($id);

        // Check access using new access control
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // AUTO-ASSIGN: If Android ticket and staff is viewing for first time
        if ($ticket->source === 'android' && 
            $ticket->assigned_to === null && 
            $currentUser->jenis_organisasi !== 'semua') {
            
            $ticket->update(['assigned_to' => $currentUser->id]);
            
            // Log activity
            activity('support')
                ->performedOn($ticket)
                ->causedBy($currentUser)
                ->withProperties([
                    'ticket_number' => $ticket->ticket_number,
                    'assigned_to' => $currentUser->name,
                    'ip' => request()->ip(),
                ])
                ->event('auto_assigned')
                ->log('Tiket Android auto-assigned kepada staff yang pertama membuka');
        }

        // Mark all messages as read by current user
        foreach ($ticket->messages as $message) {
            if ($message->user_id !== $currentUser->id) {
                $message->markAsReadBy($currentUser->id);
            }
        }

        // Mark ticket as 'dalam_proses' if admin is viewing for first time
        if ($currentUser->jenis_organisasi === 'semua' && $ticket->status === 'baru') {
            $ticket->update(['status' => 'dalam_proses']);
        }

        // Build serialized payload with labels/colors to avoid undefined in frontend
        $ticketData = [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
            'priority_label' => $ticket->priority_label,
            'priority_color' => $ticket->priority_color,
            'status' => $ticket->status,
            'status_label' => $ticket->status_label,
            'status_color' => $ticket->status_color,
            'source' => $ticket->source,
            'organization_name' => $ticket->organization_name,
            'opened_ago' => optional($ticket->created_at)->diffForHumans(),
            'message_count' => $ticket->messages->count(),
            'ip_address' => $ticket->ip_address,
            'device' => $ticket->device,
            'platform' => $ticket->platform,
            'latitude' => $ticket->latitude,
            'longitude' => $ticket->longitude,
            'creator' => [
                'id' => $ticket->creator?->id ?? null,
                'name' => $ticket->creator?->name ?? 'N/A',
                'email' => $ticket->creator?->email ?? null,
            ],
            'assigned_to' => $ticket->assigned_to,
            'assigned_admin' => $ticket->assignedAdmin ? [
                'id' => $ticket->assignedAdmin->id,
                'name' => $ticket->assignedAdmin->name,
                'email' => $ticket->assignedAdmin->email,
            ] : null,
            'participants' => $ticket->participants->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'email' => $p->email,
                    'role' => $p->pivot->role,
                    'added_at' => optional($p->pivot->added_at)->diffForHumans(),
                ];
            })->values(),
            'messages' => $ticket->messages->map(function ($m) {
                // Determine organization label for badge
                $organizationLabel = null;
                if ($m->user && $m->role !== 'admin' && $m->role !== 'sistem') {
                    if ($m->user->jenis_organisasi === 'stesen' && $m->user->organisasi_id) {
                        $stesen = \App\Models\RisdaStesen::find($m->user->organisasi_id);
                        $organizationLabel = $stesen ? $stesen->nama_stesen : 'Stesen';
                    } elseif ($m->user->jenis_organisasi === 'bahagian' && $m->user->organisasi_id) {
                        $bahagian = \App\Models\RisdaBahagian::find($m->user->organisasi_id);
                        $organizationLabel = $bahagian ? $bahagian->nama_bahagian : 'Bahagian';
                    }
                }
                
                return [
                    'id' => $m->id,
                    'message' => $m->message,
                    'role' => $m->role,
                    'role_label' => $m->role_label,
                    'organization_label' => $organizationLabel,
                    'created_at' => optional($m->created_at)->toISOString(),
                    'attachments' => $m->attachments ?? [],
                    'ip_address' => $m->ip_address,
                    'location' => $m->location,
                    'latitude' => $m->latitude,
                    'longitude' => $m->longitude,
                    'user' => $m->user ? [ 'name' => $m->user->name ] : null,
                ];
            })->values(),
        ];

        return response()->json([
            'success' => true,
            'ticket' => $ticketData,
            'current_user' => [
                'id' => $currentUser->id,
                'jenis_organisasi' => $currentUser->jenis_organisasi,
            ],
        ]);
    }

    /**
     * Store a new ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:rendah,sederhana,tinggi,kritikal',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max
        ]);

        $currentUser = Auth::user();

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
            'jenis_organisasi' => $currentUser->jenis_organisasi,
            'organisasi_id' => $currentUser->organisasi_id,
            'created_by' => $currentUser->id,
            'assigned_to' => $currentUser->id, // Auto-assign web tickets to creator
            'source' => 'web',
            'ip' => $request->ip(),
            'device' => substr($request->userAgent() ?? '', 0, 190),
            'platform' => $request->header('X-Platform') ?? 'web',
            // latitude/longitude could be posted later by apps
        ]);

        // Get location from IP
        $locationData = $this->getLocationFromIP($request->ip());

        // Create initial message
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $currentUser->id,
            'role' => $currentUser->jenis_organisasi === 'semua' ? 'admin' : 'pengguna',
            'message' => $request->message,
            'attachments' => $attachments,
            'ip_address' => $request->ip(),
            'location' => $locationData['location'] ?? null,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
        ]);

        // Update last_reply_at
        $ticket->update(['last_reply_at' => now()]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'organization' => $ticket->organization_name,
                'ip' => $request->ip(),
            ])
            ->event('created')
            ->log('Tiket sokongan baru telah dicipta');

        // Send notification based on ticket type
        if ($currentUser->jenis_organisasi !== 'semua') {
            // Staff created ticket → Notify all administrators
            $adminUsers = User::where('jenis_organisasi', 'semua')->pluck('id')->toArray();
            if (!empty($adminUsers)) {
                $this->sendNotification(
                    $adminUsers,
                    'support_ticket',
                    'Tiket Sokongan Baru dari ' . $ticket->organization_name,
                    "{$currentUser->name}: {$ticket->subject}",
                    route('help.hubungi-sokongan'),
                    ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'priority' => $ticket->priority]
                );
            }
        } else {
            // Admin created ticket → Notify assigned person (if different)
            if ($ticket->assigned_to && $ticket->assigned_to !== $currentUser->id) {
                $this->sendNotification(
                    $ticket->assigned_to,
                    'support_ticket',
                    'Tiket Sokongan Baru',
                    "Tiket #{$ticket->ticket_number}: {$ticket->subject}",
                    route('help.hubungi-sokongan'),
                    ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket sokongan berjaya dicipta.',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Reply to a ticket
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Check access using new access control
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = $path;
            }
        }

        // Get location from IP (optional - will be null if API fails)
        $locationData = $this->getLocationFromIP($request->ip());

        // Create message
        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $currentUser->id,
            'role' => $currentUser->jenis_organisasi === 'semua' ? 'admin' : 'pengguna',
            'message' => $request->message,
            'attachments' => $attachments,
            'ip_address' => $request->ip(),
            'location' => $locationData['location'] ?? null,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
        ]);

        // Update ticket status and last_reply_at
        $newStatus = $currentUser->jenis_organisasi === 'semua' ? 'dijawab' : 'dalam_proses';
        $ticket->update([
            'status' => $newStatus,
            'last_reply_at' => now(),
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'reply_role' => $message->role,
                'message_preview' => substr($request->message, 0, 100),
                'has_attachments' => !empty($attachments),
                'ip' => $request->ip(),
            ])
            ->event('replied')
            ->log('Balasan baru ditambah ke tiket sokongan');

        // Notify all relevant users EXCEPT current user
        $notifyUsers = collect([
            $ticket->created_by,
            $ticket->assigned_to,
        ])
        ->merge($ticket->participants->pluck('id'));
        
        // If ticket from staff, also notify all administrators
        if ($ticket->jenis_organisasi !== 'semua') {
            $adminUsers = User::where('jenis_organisasi', 'semua')->pluck('id');
            $notifyUsers = $notifyUsers->merge($adminUsers);
        }
        
        $notifyUsers = $notifyUsers
            ->unique()
            ->filter(fn($id) => $id && $id !== $currentUser->id)
            ->values()
            ->toArray();

        if (!empty($notifyUsers)) {
            $this->sendNotification(
                $notifyUsers,
                'support_reply',
                'Balasan Baru - ' . $ticket->ticket_number,
                "{$currentUser->name} telah membalas: " . substr($request->message, 0, 50) . '...',
                route('help.hubungi-sokongan'),
                ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan berjaya dihantar.',
            'reply' => $message,
        ]);
    }

    /**
     * Assign ticket to an admin
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Only administrators can assign tickets
        if ($currentUser->jenis_organisasi !== 'semua') {
            abort(403, 'Only administrators can assign tickets.');
        }

        $oldAssignee = $ticket->assignedAdmin;
        $newAssignee = User::find($request->assigned_to);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'dalam_proses',
        ]);

        // Create system message
        $systemMessage = "Tiket ini telah ditugaskan kepada {$newAssignee->name}.";
        if ($oldAssignee) {
            $systemMessage = "Tiket ini telah dipindahkan dari {$oldAssignee->name} kepada {$newAssignee->name}.";
        }

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'role' => 'sistem',
            'message' => $systemMessage,
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'old_assignee' => $oldAssignee?->name ?? 'Tiada',
                'new_assignee' => $newAssignee->name,
                'ip' => request()->ip(),
            ])
            ->event('assigned')
            ->log('Tiket sokongan telah ditugaskan');

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya ditugaskan.',
        ]);
    }

    /**
     * Escalate ticket priority
     */
    public function escalate(Request $request, $id)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Allow both admin and staff to escalate, but enforce org scope for staff
        if ($currentUser->jenis_organisasi !== 'semua') {
            if ($ticket->jenis_organisasi !== $currentUser->jenis_organisasi || $ticket->organisasi_id !== $currentUser->organisasi_id) {
                abort(403, 'Unauthorized');
            }
        }

        $oldPriority = $ticket->priority;
        $oldStatus = $ticket->status;

        // Ensure organisasi_id is set (for tickets that might have NULL)
        // This allows staff to still see the ticket after escalation
        $updateData = [
            'priority' => 'kritikal',
            'status' => 'escalated',
        ];
        
        // If organisasi_id is NULL, set it from current user (staff who escalated)
        if (!$ticket->organisasi_id && $currentUser->jenis_organisasi !== 'semua') {
            $updateData['organisasi_id'] = $currentUser->organisasi_id;
            $updateData['jenis_organisasi'] = $currentUser->jenis_organisasi;
        }

        $ticket->update($updateData);

        // Create system message
        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'role' => 'sistem',
            'message' => "Tiket ini telah di-escalate kepada prioriti KRITIKAL oleh {$currentUser->name}.",
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'old_priority' => $oldPriority,
                'new_priority' => 'kritikal',
                'old_status' => $oldStatus,
                'new_status' => 'escalated',
                'ip' => request()->ip(),
            ])
            ->event('escalated')
            ->log('Tiket sokongan telah di-escalate');

        // Notify all administrators
        $adminUsers = User::where('jenis_organisasi', 'semua')->pluck('id')->toArray();
        if (!empty($adminUsers)) {
            $this->sendNotification(
                $adminUsers,
                'support_escalated',
                '🚨 Tiket Di-Escalate',
                "{$currentUser->name} escalate tiket #{$ticket->ticket_number}: {$ticket->subject}",
                route('help.hubungi-sokongan'),
                ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number, 'priority' => 'kritikal']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya di-escalate.',
        ]);
    }

    /**
     * Close ticket
     */
    public function close(Request $request, $id)
    {
        $request->validate([
            'resolution_note' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Only administrators can close tickets
        if ($currentUser->jenis_organisasi !== 'semua') {
            abort(403, 'Only administrators can close tickets.');
        }

        $ticket->update([
            'status' => 'ditutup',
            'closed_at' => now(),
        ]);

        // Create system message
        $systemMessage = "Tiket ini telah ditutup oleh {$currentUser->name}.";
        if ($request->resolution_note) {
            $systemMessage .= "\n\nNota: {$request->resolution_note}";
        }

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'role' => 'sistem',
            'message' => $systemMessage,
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'resolution_note' => $request->resolution_note ?? 'Tiada nota',
                'ip' => request()->ip(),
            ])
            ->event('closed')
            ->log('Tiket sokongan telah ditutup');

        // Notify creator & participants
        $notifyUsers = collect([$ticket->created_by])
            ->merge($ticket->participants->pluck('id'))
            ->unique()
            ->filter(fn($id) => $id && $id !== $currentUser->id)
            ->values()
            ->toArray();

        if (!empty($notifyUsers)) {
            $this->sendNotification(
                $notifyUsers,
                'support_closed',
                'Tiket Diselesaikan',
                "Tiket #{$ticket->ticket_number} telah ditutup oleh {$currentUser->name}",
                route('help.hubungi-sokongan'),
                ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya ditutup.',
        ]);
    }

    /**
     * Reopen a closed ticket
     */
    public function reopen($id)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Check access
        if ($currentUser->jenis_organisasi !== 'semua') {
            if ($ticket->jenis_organisasi !== $currentUser->jenis_organisasi || 
                $ticket->organisasi_id !== $currentUser->organisasi_id) {
                abort(403, 'Unauthorized access to this ticket.');
            }
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
            'message' => "Tiket ini telah dibuka semula oleh {$currentUser->name}.",
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'ip' => request()->ip(),
            ])
            ->event('reopened')
            ->log('Tiket sokongan telah dibuka semula');

        return response()->json([
            'success' => true,
            'message' => 'Tiket berjaya dibuka semula.',
        ]);
    }

    /**
     * Delete a ticket (soft delete or hard delete)
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Only administrators can delete tickets
        if ($currentUser->jenis_organisasi !== 'semua') {
            abort(403, 'Only administrators can delete tickets.');
        }

        // Log before delete
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'status' => $ticket->status,
                'ip' => request()->ip(),
            ])
            ->event('deleted')
            ->log('Tiket sokongan telah dipadam');

        // Delete all messages first
        $ticket->messages()->delete();

        // Delete ticket
        $ticket->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tiket berjaya dipadam.',
            ]);
        }

        return redirect()->route('help.hubungi-sokongan')
            ->with('success', 'Tiket berjaya dipadam.');
    }

    /**
     * Assign/Reassign ticket to a user
     */
    public function assignUser(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id', // Now optional
        ]);

        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Check access
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $message = 'Changes saved successfully';

        // Only update assignment if assigned_to is provided
        if ($request->filled('assigned_to')) {
            // Check permission: Admin can assign to anyone, Staff can only assign within their org
            $newAssignee = User::findOrFail($request->assigned_to);
            
            if ($currentUser->jenis_organisasi !== 'semua') {
                // Staff can only assign to users in same organization
                if ($newAssignee->jenis_organisasi !== $currentUser->jenis_organisasi ||
                    $newAssignee->organisasi_id !== $currentUser->organisasi_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda hanya boleh assign kepada staff dalam organisasi yang sama.',
                    ], 403);
                }
            }

            $oldAssignee = $ticket->assignedAdmin;

            // Update assigned_to
            $ticket->update(['assigned_to' => $request->assigned_to]);

            // Log activity
            activity('support')
                ->performedOn($ticket)
                ->causedBy($currentUser)
                ->withProperties([
                    'ticket_number' => $ticket->ticket_number,
                    'old_assignee' => $oldAssignee ? $oldAssignee->name : 'Tiada',
                    'new_assignee' => $newAssignee->name,
                    'ip' => request()->ip(),
                ])
                ->event('assigned')
                ->log('Tiket telah di-assign kepada ' . $newAssignee->name);

            // Notify new assignee
            if ($newAssignee->id !== $currentUser->id) {
                $this->sendNotification(
                    $newAssignee->id,
                    'support_assigned',
                    'Tiket Di-assign Kepada Anda',
                    "Tiket #{$ticket->ticket_number}: {$ticket->subject}",
                    route('help.hubungi-sokongan'),
                    ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
                );
            }

            $message = 'Tiket berjaya di-assign kepada ' . $newAssignee->name;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Add participant to ticket discussion
     */
    public function addParticipant(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Check access
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $participant = User::findOrFail($request->user_id);

        // Check permission: Admin can add anyone, Staff can only add from their org
        if ($currentUser->jenis_organisasi !== 'semua') {
            if ($participant->jenis_organisasi !== $currentUser->jenis_organisasi ||
                $participant->organisasi_id !== $currentUser->organisasi_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya boleh add participant dari organisasi yang sama.',
                ], 403);
            }
        }

        // Check if already participant
        if ($ticket->participants()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => $participant->name . ' sudah menjadi participant.',
            ], 400);
        }

        // Add participant
        $ticket->participants()->attach($request->user_id, [
            'role' => 'viewer',
            'added_by' => $currentUser->id,
            'added_at' => now(),
        ]);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'participant' => $participant->name,
                'ip' => request()->ip(),
            ])
            ->event('participant_added')
            ->log('Participant ditambah: ' . $participant->name);

        // Notify the new participant
        $this->sendNotification(
            $participant->id,
            'support_participant',
            'Anda Ditambah ke Tiket',
            "{$currentUser->name} menambah anda ke tiket #{$ticket->ticket_number}: {$ticket->subject}",
            route('help.hubungi-sokongan'),
            ['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]
        );

        return response()->json([
            'success' => true,
            'message' => $participant->name . ' berjaya ditambah sebagai participant.',
        ]);
    }

    /**
     * Remove participant from ticket discussion
     */
    public function removeParticipant(Request $request, $id, $userId)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::findOrFail($id);

        // Check access
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $participant = User::findOrFail($userId);

        // Remove participant
        $ticket->participants()->detach($userId);

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'participant' => $participant->name,
                'ip' => request()->ip(),
            ])
            ->event('participant_removed')
            ->log('Participant dibuang: ' . $participant->name);

        return response()->json([
            'success' => true,
            'message' => $participant->name . ' berjaya dibuang dari participant.',
        ]);
    }

    /**
     * Export ticket chat history
     */
    public function export($id)
    {
        $currentUser = Auth::user();
        $ticket = SupportTicket::with(['creator', 'assignedAdmin', 'messages.user', 'participants'])->findOrFail($id);

        // Check access
        if (!$ticket->canBeAccessedBy($currentUser)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        // Log activity
        activity('support')
            ->performedOn($ticket)
            ->causedBy($currentUser)
            ->withProperties([
                'ticket_number' => $ticket->ticket_number,
                'format' => 'text',
                'ip' => request()->ip(),
            ])
            ->event('exported')
            ->log('Chat history tiket sokongan telah di-eksport');

        // Generate text content
        $content = "RISDA ODOMETER - SUPPORT TICKET EXPORT\n";
        $content .= "=====================================\n\n";
        $content .= "Ticket Number: {$ticket->ticket_number}\n";
        $content .= "Subject: {$ticket->subject}\n";
        $content .= "Category: {$ticket->category}\n";
        $content .= "Priority: {$ticket->priority_label}\n";
        $content .= "Status: {$ticket->status_label}\n";
        $content .= "Created: {$ticket->created_at->format('d/m/Y H:i')}\n";
        $content .= "Creator: {$ticket->creator->name}\n";
        
        if ($ticket->assignedAdmin) {
            $content .= "Assigned To: {$ticket->assignedAdmin->name}\n";
        }
        
        if ($ticket->participants->count() > 0) {
            $content .= "Participants: " . $ticket->participants->pluck('name')->join(', ') . "\n";
        }
        
        $content .= "\n=====================================\n";
        $content .= "CHAT HISTORY\n";
        $content .= "=====================================\n\n";

        foreach ($ticket->messages as $msg) {
            $userName = $msg->user ? $msg->user->name : 'Sistem';
            $timestamp = $msg->created_at->format('d/m/Y H:i:s');
            $role = strtoupper($msg->role_label);
            
            $content .= "[{$timestamp}] {$userName} ({$role})\n";
            $content .= "{$msg->message}\n";
            
            if ($msg->attachments && count($msg->attachments) > 0) {
                $content .= "Attachments:\n";
                foreach ($msg->attachments as $att) {
                    $fileName = basename($att);
                    $content .= "  - {$fileName}\n";
                }
            }
            
            $content .= "\n" . str_repeat('-', 60) . "\n\n";
        }

        $content .= "\nExported by: {$currentUser->name}\n";
        $content .= "Export Date: " . now()->format('d/m/Y H:i:s') . "\n";
        
        // Return as downloadable text file
        $filename = "ticket-{$ticket->ticket_number}-" . now()->format('Ymd-His') . ".txt";
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Get location from IP address using ip-api.com
     */
    private function getLocationFromIP($ip)
    {
        // Skip for localhost/private IPs
        if ($ip === '127.0.0.1' || $ip === 'localhost' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return [
                'location' => 'Local Network',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        try {
            // Use ip-api.com free API (no key required, 45 req/min limit)
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");
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
            // Silently fail - location is optional
            \Log::info("Failed to get location for IP {$ip}: " . $e->getMessage());
        }

        return [
            'location' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    /**
     * Send notification to user(s)
     */
    private function sendNotification($userId, $type, $title, $message, $actionUrl = null, $data = [])
    {
        // Handle array of user IDs
        $userIds = is_array($userId) ? $userId : [$userId];
        
        foreach ($userIds as $uid) {
            if ($uid) {
                Notification::create([
                    'user_id' => $uid,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'action_url' => $actionUrl,
                    'data' => $data,
                ]);
            }
        }
    }
}
