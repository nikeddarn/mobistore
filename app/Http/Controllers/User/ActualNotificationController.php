<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Support\Notifications\Fabtics\UserActualNotificationsFabric;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ActualNotificationController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var UserActualNotificationsFabric
     */
    private $userActualNotificationsFabric;

    /**
     * MessageController constructor.
     * @param Request $request
     * @param UserActualNotificationsFabric $userActualNotificationsFabric
     */
    public function __construct(Request $request, UserActualNotificationsFabric $userActualNotificationsFabric)
    {
        $this->request = $request;
        $this->userActualNotificationsFabric = $userActualNotificationsFabric;
    }

    /**
     * Show unread user messages.
     *
     * @return View
     */
    public function show(): View
    {
        $user = $this->request->user('web');

        $notifications = $this->userActualNotificationsFabric->getRepository()->getNotifications($user);

        $perPage = config('shop.user_items_per_page_count.active_items');

        $paginator = $this->createPaginator($notifications, $perPage, route('user_notifications.show.unread'));

        $notificationsData = $this->userActualNotificationsFabric->getViewer()->getNotificationsData($paginator);

        return view('content.user.notifications.unread.index')
            ->with([
                'commonMetaData' => $this->getCommonViewData(),
                'userUnreadNotifications' => $notificationsData,
            ]);
    }

    /**
     * Mark notification as read.
     *
     * @param int $notificationId
     */
    public function markAsRead($notificationId)
    {
        $user = $this->request->user('web');

        $this->userActualNotificationsFabric->getHandler()->markAsRead($user, $notificationId);


    }

    /**
     * Get common view data.
     *
     * @return array
     */
    private function getCommonViewData(): array
    {
        return [
            'title' => trans('meta.title.user.notifications'),
        ];
    }

    /**
     * Create paginator.
     *
     * @param Collection $notifications
     * @param int $perPage
     * @param string $urlPath
     * @return LengthAwarePaginator
     */
    private function createPaginator(Collection $notifications, int $perPage, string $urlPath): LengthAwarePaginator
    {
        $currentPage = $this->request->has('page') ? (int)$this->request->get('page') : 1;

        return new LengthAwarePaginator($notifications->forPage($currentPage, $perPage), $notifications->count(), $perPage, $currentPage, [
            'path' => $urlPath,
        ]);
    }
}
