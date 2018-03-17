<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * MessageController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Show user messages.
     *
     * @return View
     */
    public function showAllMessages()
    {
        return view('content.user.messages.index')->with([
            'userMessages' => $this->getUserNotifications(),
            'commonMetaData' => [
                'title' => trans('meta.title.user.messages'),
            ],
        ]);
    }

    /**
     * Mark notification as read. Show user notifications.
     *
     * @param int $messageId
     * @return $this|bool
     */
    public function markAsRead($messageId)
    {
        $user = auth('web')->user();

        $user->notifications()->where('id', $messageId)->first()->markAsRead();

        if ($this->request->ajax()) {
            return '';
        } else {
            return view('content.user.messages.index')->with([
                'userMessages' => $this->getUserNotifications(),
                'commonMetaData' => [
                    'title' => trans('meta.title.user.messages'),
                ],
            ]);
        }
    }

    /**
     * Get user notifications
     *
     * @return LengthAwarePaginator
     */
    private function getUserNotifications():LengthAwarePaginator
    {
        $user = auth('web')->user();

        return $user->notifications()->paginate(config('shop.user_messages_count'));
    }
}
