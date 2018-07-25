<?php
/**
 * User notifications handler.
 */

namespace App\Http\Support\Notifications\Handlers;


use App\Contracts\Shop\Notifications\UserNotificationsHandlerInterface;

class UserNotificationsHandler implements UserNotificationsHandlerInterface
{
    /**
     * Mark notification as read.
     *
     * @param $user
     * @param string $notificationId
     */
    public function markAsRead($user, string $notificationId)
    {
        $user->notifications()->where('id', $notificationId)->first()->markAsRead();
    }

    /**
     * Delete user's notification.
     *
     * @param $user
     * @param int $notificationId
     */
    public function deleteNotification($user, int $notificationId)
    {
        $user->notifications()->where('id', $notificationId)->delete();
    }

    /**
     * Delete all user's notifications.
     *
     * @param $user
     */
    public function deleteAllNotifications($user)
    {
        $user->notifications()->delete();
    }
}