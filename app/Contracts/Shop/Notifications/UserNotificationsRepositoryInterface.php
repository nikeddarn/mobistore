<?php
/**
 * User notifications repository interface.
 */

namespace App\Contracts\Shop\Notifications;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

interface UserNotificationsRepositoryInterface
{
    /**
     * Get invoice by it's id.
     *
     * @param $user
     * @param int $notificationId
     * @return Notification|Model
     */
    public function getByNotificationId($user,int $notificationId): Notification;

    /**
     * Get user's notifications.
     *
     * @param $user
     * @return Collection
     */
    public function getNotifications($user):Collection;
}