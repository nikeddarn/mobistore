<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Support\Notifications\Fabtics\UserNotificationsFabric;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var UserNotificationsFabric
     */
    private $userNotificationsFabric;

    /**
     * MessageController constructor.
     * @param Request $request
     * @param UserNotificationsFabric $userNotificationsFabric
     */
    public function __construct(Request $request, UserNotificationsFabric $userNotificationsFabric)
    {
        $this->request = $request;
        $this->userNotificationsFabric = $userNotificationsFabric;
    }

    /**
     * Show all user messages.
     *
     * @return View
     */
    public function show()
    {
        $user = $this->request->user('web');

        $notifications = $this->userNotificationsFabric->getRepository()->getNotifications($user);

        $perPage = config('shop.user_items_per_page_count.all_items');

        $paginator = $this->createPaginator($notifications, $perPage, route('user_notifications.show.all'));

        $notificationsData = $this->userNotificationsFabric->getViewer()->getNotificationsData($paginator);

        return view('content.user.notifications.all.index')
            ->with([
                'commonMetaData' => $this->getCommonViewData(),
                'userAllNotifications' => $notificationsData,
            ]);
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
