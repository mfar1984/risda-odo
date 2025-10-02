<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use App\Models\FcmToken;
use App\Models\Notification as AppNotification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/jara-risda-e67243fd5a15.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        try {
            // Get user's FCM tokens
            $tokens = FcmToken::where('user_id', $userId)->pluck('token')->toArray();

            if (empty($tokens)) {
                Log::info("No FCM tokens found for user {$userId}");
                return false;
            }

            // Create notification in database
            $notification = AppNotification::create([
                'user_id' => $userId,
                'type' => $data['type'] ?? 'general',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

            // Prepare Firebase notification
            $firebaseNotification = FirebaseNotification::create($title, $body);

            // Add notification ID to data payload
            $data['notification_id'] = $notification->id;

            // Send to all user's devices
            $messages = [];
            foreach ($tokens as $token) {
                $messages[] = CloudMessage::withTarget('token', $token)
                    ->withNotification($firebaseNotification)
                    ->withData($data);
            }

            // Send multi-cast
            if (count($messages) > 0) {
                $sendReport = $this->messaging->sendAll($messages);
                
                Log::info("FCM sent to user {$userId}: {$sendReport->successes()->count()} successful, {$sendReport->failures()->count()} failed");

                // Remove invalid tokens
                foreach ($sendReport->failures()->getItems() as $failure) {
                    if ($failure->error()->isInvalidArgument() || $failure->error()->isNotFound()) {
                        $invalidToken = $failure->target()->value();
                        FcmToken::where('token', $invalidToken)->delete();
                        Log::info("Removed invalid FCM token: {$invalidToken}");
                    }
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers(array $userIds, $title, $body, $data = [])
    {
        foreach ($userIds as $userId) {
            $this->sendToUser($userId, $title, $body, $data);
        }
    }

    /**
     * Register FCM token for a user
     */
    public function registerToken($userId, $token, $deviceType = null, $deviceId = null)
    {
        return FcmToken::updateOrCreate(
            ['user_id' => $userId, 'token' => $token],
            [
                'device_type' => $deviceType,
                'device_id' => $deviceId,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Remove FCM token
     */
    public function removeToken($token)
    {
        return FcmToken::where('token', $token)->delete();
    }
}

