<?php
/**
 * User notifications repository.
 */

namespace App\Http\Support\Notifications\Repositories;


use App\Contracts\Shop\Notifications\UserNotificationsRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class UserNotificationsRepository implements UserNotificationsRepositoryInterface
{
    /**
     * Get invoice by it's id.
     *
     * @param $user
     * @param int $notificationId
     * @return Notification|Model
     */
    public function getByNotificationId($user,int $notificationId): Notification
    {
        return static::makeQuery($user)->where('id', $notificationId)->first();
    }

    /**
     * Get user's notifications.
     *
     * @param $user
     * @return Collection
     */
    public function getNotifications($user):Collection
    {
        return static::makeQuery($user)->get();
    }

    /**
     * Make notifications query.
     *
     * @param $user
     * @return Builder
     */
    protected function makeQuery($user):Builder
    {
        return $user->notifications()->getQuery()->orderByDesc('created_at');
    }
}