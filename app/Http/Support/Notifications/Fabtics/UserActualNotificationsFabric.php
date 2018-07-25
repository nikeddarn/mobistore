<?php
/**
 * User actual notifications fabric.
 */

namespace App\Http\Support\Notifications\Fabtics;


use App\Contracts\Shop\Notifications\UserNotificationsHandlerInterface;
use App\Contracts\Shop\Notifications\UserNotificationsRepositoryInterface;
use App\Contracts\Shop\Notifications\UserNotificationsViewerInterface;
use App\Http\Support\Notifications\Handlers\UserNotificationsHandler;
use App\Http\Support\Notifications\Repositories\UserActualNotificationsRepository;
use App\Http\Support\Notifications\Viewers\UserNotificationViewer;

class UserActualNotificationsFabric
{
    /**
     * @var UserNotificationsHandler
     */
    private $userNotificationsHandler;
    /**
     * @var UserNotificationViewer
     */
    private $userNotificationViewer;
    /**
     * @var UserActualNotificationsRepository
     */
    private $userActualNotificationsRepository;

    /**
     * UserNotificationsFabric constructor.
     * @param UserActualNotificationsRepository $userActualNotificationsRepository
     * @param UserNotificationsHandler $userNotificationsHandler
     * @param UserNotificationViewer $userNotificationViewer
     */
    public function __construct(UserActualNotificationsRepository $userActualNotificationsRepository, UserNotificationsHandler $userNotificationsHandler, UserNotificationViewer $userNotificationViewer)
    {

        $this->userNotificationsHandler = $userNotificationsHandler;
        $this->userNotificationViewer = $userNotificationViewer;
        $this->userActualNotificationsRepository = $userActualNotificationsRepository;
    }

    /**
     * Get repository.
     *
     * @return UserNotificationsRepositoryInterface
     */
    public function getRepository():UserNotificationsRepositoryInterface
    {
        return $this->userActualNotificationsRepository;
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