<?php
/**
 * User notifications handler interface
 */

namespace App\Contracts\Shop\Notifications;


interface UserNotificationsHandlerInterface
{
    /**
     * Mark notification as read.
     *
     * @param $user
     * @param string $notificationId
     */
    public function markAsRead($user, string $notificationId);
}