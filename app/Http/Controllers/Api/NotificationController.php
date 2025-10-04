<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user (mobile app)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Simple query - user's notifications OR global notifications
            $query = Notification::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            });

            // Filter by read status BEFORE pagination
            if ($request->has('unread_only') && $request->unread_only) {
                $query->whereNull('read_at');
            }

            // Get paginated results
            $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Count unread efficiently
            if ($request->has('unread_only') && $request->unread_only) {
                $unreadCount = $notifications->total();
            } else {
                $unreadCount = Notification::where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereNull('user_id');
                })
                ->whereNull('read_at')
                ->count();
            }

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
