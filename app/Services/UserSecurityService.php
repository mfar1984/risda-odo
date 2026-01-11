<?php

namespace App\Services;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\Security2FAResetNotification;
use App\Mail\SecurityForceLogoutNotification;
use App\Mail\SecurityPasswordResetNotification;
use App\Mail\SecurityAccountLockedNotification;
use App\Mail\SecurityAccountUnlockedNotification;

class UserSecurityService
{
    /**
     * Reset Two-Factor Authentication for a user
     *
     * @param User $user Target user
     * @param User $admin Administrator performing action
     * @return bool Success status
     */
    public function reset2FA(User $user, User $admin): bool
    {
        try {
            // Disable 2FA
            $user->disable2FA();

            // Log the action
            $this->logSecurityAction($admin, $user, 'admin_reset_2fa', [
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);

            // Send email notification
            $this->sendSecurityNotification($user, $admin, '2fa_reset');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to reset 2FA', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Force logout all active sessions for a user
     *
     * @param User $user Target user
     * @param User $admin Administrator performing action
     * @return int Number of sessions logged out
     */
    public function forceLogoutAllSessions(User $user, User $admin): int
    {
        try {
            // Get session count before deletion
            $sessionCount = $user->getActiveSessionsCount();

            // Invalidate all sessions
            $user->invalidateAllSessions();

            // Log the action
            $this->logSecurityAction($admin, $user, 'admin_force_logout', [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'session_count' => $sessionCount,
            ]);

            // Send email notification
            $this->sendSecurityNotification($user, $admin, 'force_logout', [
                'session_count' => $sessionCount
            ]);

            return $sessionCount;
        } catch (\Exception $e) {
            Log::error('Failed to force logout sessions', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Reset/change user password
     *
     * @param User $user Target user
     * @param User $admin Administrator performing action
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function resetPassword(User $user, User $admin, string $newPassword): bool
    {
        try {
            // Update password
            $user->update([
                'password' => $newPassword
            ]);

            // Force logout all sessions
            $user->invalidateAllSessions();

            // Log the action
            $this->logSecurityAction($admin, $user, 'admin_reset_password', [
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);

            // Send email notification with new password
            $this->sendSecurityNotification($user, $admin, 'password_reset', [
                'new_password' => $newPassword
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to reset password', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Lock user account
     *
     * @param User $user Target user
     * @param User $admin Administrator performing action
     * @param string|null $reason Reason for locking
     * @return bool Success status
     */
    public function lockAccount(User $user, User $admin, ?string $reason = null): bool
    {
        try {
            // Set status to tidak_aktif
            $user->update([
                'status' => 'tidak_aktif'
            ]);

            // Force logout all sessions
            $user->invalidateAllSessions();

            // Log the action
            $this->logSecurityAction($admin, $user, 'admin_lock_account', [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'reason' => $reason,
            ]);

            // Send email notification
            $this->sendSecurityNotification($user, $admin, 'account_locked', [
                'reason' => $reason
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to lock account', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Unlock user account
     *
     * @param User $user Target user
     * @param User $admin Administrator performing action
     * @return bool Success status
     */
    public function unlockAccount(User $user, User $admin): bool
    {
        try {
            // Set status to aktif
            $user->update([
                'status' => 'aktif'
            ]);

            // Log the action
            $this->logSecurityAction($admin, $user, 'admin_unlock_account', [
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);

            // Send email notification
            $this->sendSecurityNotification($user, $admin, 'account_unlocked');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unlock account', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get security logs for a user
     *
     * @param User $user Target user
     * @param int $perPage Pagination limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSecurityLogs(User $user, int $perPage = 20)
    {
        $securityEvents = [
            'admin_reset_2fa',
            'admin_force_logout',
            'admin_reset_password',
            'admin_lock_account',
            'admin_unlock_account',
            'login_success',
            'login_failed',
            'login_blocked',
            'password_changed',
            '2fa_enabled',
            '2fa_disabled',
        ];

        return Activity::where(function ($query) use ($user, $securityEvents) {
            // Get logs where user is the subject (affected user) - for admin actions
            $query->where(function ($q) use ($user, $securityEvents) {
                $q->where('subject_type', User::class)
                  ->where('subject_id', $user->id)
                  ->whereIn('log_name', $securityEvents);
            })
            // OR where user is the causer (login events use causer_id and event field)
            ->orWhere(function ($q) use ($user, $securityEvents) {
                $q->where('causer_type', User::class)
                  ->where('causer_id', $user->id)
                  ->whereIn('event', $securityEvents);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    /**
     * Log security action to audit log
     *
     * @param User $admin Administrator performing action
     * @param User $user Target user
     * @param string $action Action type
     * @param array $details Additional details
     * @return void
     */
    protected function logSecurityAction(User $admin, User $user, string $action, array $details = []): void
    {
        try {
            $description = $this->formatLogDescription($admin, $user, $action, $details);

            activity($action)
                ->causedBy($admin)
                ->performedOn($user)
                ->withProperties(array_merge($details, [
                    'target_user_id' => $user->id,
                    'target_user_name' => $user->name,
                    'target_user_email' => $user->email,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]))
                ->log($description);
        } catch (\Exception $e) {
            Log::error('Failed to log security action', [
                'admin_id' => $admin->id,
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format log description
     *
     * @param User $admin
     * @param User $user
     * @param string $action
     * @param array $details
     * @return string
     */
    protected function formatLogDescription(User $admin, User $user, string $action, array $details): string
    {
        $actionDescriptions = [
            'admin_reset_2fa' => 'reset 2FA for',
            'admin_force_logout' => 'forced logout all sessions for',
            'admin_reset_password' => 'reset password for',
            'admin_lock_account' => 'locked account for',
            'admin_unlock_account' => 'unlocked account for',
        ];

        $actionText = $actionDescriptions[$action] ?? $action;
        $description = "Administrator {$admin->name} {$actionText} user {$user->name}";

        if (isset($details['reason']) && $details['reason']) {
            $description .= " (Reason: {$details['reason']})";
        }

        if (isset($details['session_count'])) {
            $description .= " ({$details['session_count']} sessions)";
        }

        return $description;
    }

    /**
     * Send email notification to user
     *
     * @param User $user Target user
     * @param User $admin Administrator who performed action
     * @param string $action Action type
     * @param array $data Additional data for email
     * @return void
     */
    protected function sendSecurityNotification(User $user, User $admin, string $action, array $data = []): void
    {
        try {
            $timestamp = now()->format('d/m/Y H:i:s');

            switch ($action) {
                case '2fa_reset':
                    Mail::to($user->email)->queue(
                        new Security2FAResetNotification($user, $admin, $timestamp)
                    );
                    break;

                case 'force_logout':
                    Mail::to($user->email)->queue(
                        new SecurityForceLogoutNotification(
                            $user,
                            $admin,
                            $data['session_count'] ?? 0,
                            $timestamp
                        )
                    );
                    break;

                case 'password_reset':
                    Mail::to($user->email)->queue(
                        new SecurityPasswordResetNotification(
                            $user,
                            $admin,
                            $data['new_password'] ?? '',
                            $timestamp
                        )
                    );
                    break;

                case 'account_locked':
                    Mail::to($user->email)->queue(
                        new SecurityAccountLockedNotification(
                            $user,
                            $admin,
                            $data['reason'] ?? null,
                            $timestamp
                        )
                    );
                    break;

                case 'account_unlocked':
                    Mail::to($user->email)->queue(
                        new SecurityAccountUnlockedNotification($user, $admin, $timestamp)
                    );
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send security notification', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}
