<?php
/**
 * User notifications fabric.
 */

namespace App\Http\Support\Notifications\Fabtics;


use App\Contracts\Shop\Notifications\UserNotificationsHandlerInterface;
use App\Contracts\Shop\Notifications\UserNotificationsRepositoryInterface;
use App\Contracts\Shop\Notifications\UserNotificationsViewerInterface;
use App\Http\Support\Notifications\Handlers\UserNotificationsHandler;
use App\Http\Support\Notifications\Repositories\UserNotificationsRepository;
use App\Http\Support\Notifications\Viewers\UserNotificationViewer;

class UserNotificationsFabric
{
    /**
     * @var UserNotificationsRepository
     */
    private $userNotificationsRepository;
    /**
     * @var UserNotificationsHandler
     */
    private $userNotificationsHandler;
    /**
     * @var UserNotificationViewer
     */
    private $userNotificationViewer;

    /**
     * UserNotificationsFabric constructor.
     * @param UserNotificationsRepository $userNotificationsRepository
     * @param UserNotificationsHandler $userNotificationsHandler
     * @param UserNotificationViewer $userNotificationViewer
     */
    public function __construct(UserNotificationsRepository $userNotificationsRepository, UserNotificationsHandler $userNotificationsHandler, UserNotificationViewer $userNotificationViewer)
    {

        $this->userNotificationsRepository = $userNotificationsRepository;
        $this->userNotificationsHandler = $userNotificationsHandler;
        $this->userNotificationViewer = $userNotificationViewer;
    }

    /**
     * Get repository.
     *
     * @return UserNotificationsRepositoryInterface
     */
    public function getRepository():UserNotificationsRepositoryInterface
    {
        return $this->userNotificationsRepository;
    }

    /**
     * Get handler.
     *
     * @return UserNotificationsHandlerInterface
     */
    public function getHandler():UserNotificationsHandlerInterface
    {
        return $this->userNotificationsHandler;
    }

    /**
     * Get viewer.
     *
     * @return UserNotificationsViewerInterface
     */
    public function getViewer():UserNotificationsViewerInterface
    {
        return $this->userNotificationViewer;
    }
}