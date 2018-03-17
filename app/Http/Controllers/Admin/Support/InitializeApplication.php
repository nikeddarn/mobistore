<?php
/**
 * Fill libraries.
 * insert root user.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Badges\BadgeTypes;
use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Delivery\DeliveryTypesInterface;
use App\Contracts\Shop\Delivery\PostServicesInterface;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Reclamations\RejectReclamationReasons;
use App\Contracts\Shop\Roles\UserRolesInterface;
use App\Models\Badge;
use App\Models\Color;
use App\Models\Currency;
use App\Models\DeliveryStatus;
use App\Models\DeliveryType;
use App\Models\InvoiceStatus;
use App\Models\InvoiceType;
use App\Models\PostService;
use App\Models\Quality;
use App\Models\RejectReclamationReason;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use ReflectionClass;

class InitializeApplication implements UserRolesInterface
{
    /**
     * @var Role
     */
    private $role;

    /**
     * @var Color
     */
    private $color;

    /**
     * @var Quality
     */
    private $quality;

    /**
     * @var User
     */
    private $user;


    /**
     * @var InvoiceType
     */
    private $invoiceType;


    /**
     * @var Badge
     */
    private $badge;

    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var \App\Models\DeliveryStatus
     */
    private $deliveryStatus;
    /**
     * @var RejectReclamationReason
     */
    private $rejectReclamationReason;
    /**
     * @var DeliveryType
     */
    private $deliveryType;
    /**
     * @var PostService
     */
    private $postService;
    /**
     * @var UserRole
     */
    private $userRole;
    /**
     * @var InvoiceStatus
     */
    private $invoiceStatus;

    /**
     * InitializeApplication constructor.
     * @param Role $role
     * @param Color $color
     * @param Quality $quality
     * @param User $user
     * @param InvoiceType $invoiceType
     * @param Badge $badge
     * @param Currency $currency
     * @param DeliveryStatus $deliveryStatus
     * @param RejectReclamationReason $rejectReclamationReason
     * @param DeliveryType $deliveryType
     * @param PostService $postService
     * @param UserRole $userRole
     * @param InvoiceStatus $invoiceStatus
     */
    public function __construct(Role $role, Color $color, Quality $quality, User $user, InvoiceType $invoiceType, Badge $badge, Currency $currency, DeliveryStatus $deliveryStatus, RejectReclamationReason $rejectReclamationReason, DeliveryType $deliveryType, PostService $postService, UserRole $userRole, InvoiceStatus $invoiceStatus)
    {

        $this->role = $role;
        $this->color = $color;
        $this->quality = $quality;
        $this->user = $user;
        $this->invoiceType = $invoiceType;
        $this->badge = $badge;
        $this->currency = $currency;
        $this->deliveryStatus = $deliveryStatus;
        $this->rejectReclamationReason = $rejectReclamationReason;
        $this->deliveryType = $deliveryType;
        $this->postService = $postService;
        $this->userRole = $userRole;
        $this->invoiceStatus = $invoiceStatus;
    }

    /**
     * Fill database libraries.
     *
     * @return void
     */
    public function fillLibraries()
    {
        $this->color->insert(require database_path('setup/colors.php'));

        $this->quality->insert(require database_path('setup/quality.php'));

        $this->insertInvoiceTypes();

        $this->insertInvoiceStatus();

        $this->insertBadges();

        $this->insertCurrencies();

        $this->insertDeliveryStatus();

        $this->insertDeliveryTypes();

        $this->insertPostServices();

        $this->insertRejectReclamationReasons();

        $this->insertUserRoles();

    }

    /**
     * Create root user.
     *
     * @return void
     */
    public function insertRootUser()
    {
        $user = $this->user->create([
            'name' => 'Nikeddarn',
            'email' => 'nikeddarn@gmail.com',
            'password' => bcrypt('assodance'),
        ]);

        $this->userRole->create([
            'users_id' => $user->id,
            'roles_id' => self::ROOT,
            'general' => 1,
        ]);

        $this->userRole->create([
            'users_id' => $user->id,
            'roles_id' => self::USER_MANAGER,
            'general' => 1,
        ]);

        $this->userRole->create([
            'users_id' => $user->id,
            'roles_id' => self::VENDOR_MANAGER,
            'general' => 1,
        ]);

        $this->userRole->create([
            'users_id' => $user->id,
            'roles_id' => self::SERVICEMAN,
            'general' => 1,
        ]);

        $this->userRole->create([
            'users_id' => $user->id,
            'roles_id' => self::STOREKEEPER,
            'general' => 1,
        ]);
    }

    /**
     * Insert invoice types from interface.
     */
    private function insertInvoiceTypes()
    {
        $types = require database_path('setup/invoice_types.php');

        foreach ((new ReflectionClass(InvoiceTypes::class))->getConstants() as $constantValue) {
            $this->invoiceType->create(array_merge(['id' => $constantValue], $types[$constantValue]));
        }
    }

    /**
     * Insert invoice status from interface.
     */
    private function insertInvoiceStatus()
    {
        $types = require database_path('setup/invoice_status.php');

        foreach ((new ReflectionClass(InvoiceStatusInterface::class))->getConstants() as $constantValue) {
            $this->invoiceStatus->create(array_merge(['id' => $constantValue], $types[$constantValue]));
        }
    }

    /**
     * Insert badges.
     */
    public function insertBadges()
    {
        $badges = require database_path('setup/badges.php');

        foreach ((new ReflectionClass(BadgeTypes::class))->getConstants() as $constantValue) {
            $this->badge->create(array_merge(['id' => $constantValue], $badges[$constantValue]));
        }
    }

    /**
     * Insert currencies.
     */
    private function insertCurrencies()
    {
        $types = [];

        foreach ((new ReflectionClass(CurrenciesInterface::class))->getConstants() as $value) {
            $types[] = [
                'code' => $value,
            ];
        }

        $this->currency->insert($types);
    }

    /**
     * Insert delivery statuses.
     */
    private function insertDeliveryStatus()
    {
        $statuses = require database_path('setup/delivery_status.php');

        foreach ((new ReflectionClass(DeliveryStatusInterface::class))->getConstants() as $constantValue) {
            $this->deliveryStatus->create(array_merge(['id' => $constantValue], $statuses[$constantValue]));
        }
    }

    /**
     * Insert delivery types.
     */
    private function insertDeliveryTypes()
    {
        $statuses = require database_path('setup/delivery_types.php');

        foreach ((new ReflectionClass(DeliveryTypesInterface::class))->getConstants() as $constantValue) {
            $this->deliveryType->create(array_merge(['id' => $constantValue], $statuses[$constantValue]));
        }
    }

    /**
     * Insert post services.
     */
    private function insertPostServices()
    {
        $statuses = require database_path('setup/post_services.php');

        foreach ((new ReflectionClass(PostServicesInterface::class))->getConstants() as $constantValue) {
            $this->postService->create(array_merge(['id' => $constantValue], $statuses[$constantValue]));
        }
    }

    /**
     * Insert reclamation reasons.
     */
    private function insertRejectReclamationReasons()
    {
        $reasons = require database_path('setup/reject_reclamation_reasons.php');

        foreach ((new ReflectionClass(RejectReclamationReasons::class))->getConstants() as $constantValue) {
            $this->rejectReclamationReason->create(array_merge(['id' => $constantValue], $reasons[$constantValue]));
        }
    }

    /**
     * Insert user roles.
     */
    private function insertUserRoles()
    {
        foreach ((new ReflectionClass(UserRolesInterface::class))->getConstants() as  $constantName => $constantValue) {
            $this->role->create([
                'title' => ucfirst(strtolower(str_replace('_', ' ', $constantName))),
                'id' => $constantValue,
            ]);
        }
    }

}