<?php
/**
 * User notification viewer.
 */

namespace App\Http\Support\Notifications\Viewers;


use App\Contracts\Shop\Notifications\UserNotificationsViewerInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserNotificationViewer implements UserNotificationsViewerInterface
{
    /**
     * Get user notifications data for view.
     *
     * @param Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator $notifications
     * @return array
     */
    public function getNotificationsData($notifications): array
    {
        $notificationsData = [];

        if ($notifications->count()) {

            $notificationsData['notifications'] = $this->createNotificationsData($notifications);

            if ($notifications instanceof LengthAwarePaginator) {
                $notificationsData['links'] = $notifications->links();
            }
        }

        return $notificationsData;
    }

    /**
     * Create notifications data.
     *
     * @param Collection|LengthAwarePaginator $notifications
     * @return array
     */
    private function createNotificationsData($notifications): array
    {
        $notificationsData = [];

        foreach ($notifications as $notification) {
            $notificationsData[] = [
                'id' => $notification->id,
                'wasRead' => (bool)$notification->read_at,
                'createdAt' => $notification->created_at->diffForHumans(),
                'title' => $notification->data['title'],
                'message' => $notification->data['message'],
            ];
        }

        return $notificationsData;
    }
}