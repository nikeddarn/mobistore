<?php
/**
 * User notifications viewer interface
 */

namespace App\Contracts\Shop\Notifications;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserNotificationsViewerInterface
{
    /**
     * Get user notifications data for view.
     *
     * @param Collection|LengthAwarePaginator $notifications
     * @return array
     */
    public function getNotificationsData($notifications): array;
}