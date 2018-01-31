<?php
/**
 * Fill libraries.
 * insert root user.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Badges\BadgeTypes;
use App\Contracts\Shop\Delivery\DeliveryStatus;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Reclamations\RejectReclamationReasons;
use App\Models\Badge;
use App\Models\Color;
use App\Models\Currency;
use App\Models\InvoiceType;
use App\Models\Quality;
use App\Models\RejectReclamationReason;
use App\Models\Role;
use App\Models\User;
use ReflectionClass;

class InitializeApplication
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
     * InitializeApplication constructor.
     * @param Role $role
     * @param Color $color
     * @param Quality $quality
     * @param User $user
     * @param InvoiceType $invoiceType
     * @param Badge $badge
     * @param Currency $currency
     * @param \App\Models\DeliveryStatus $deliveryStatus
     * @param RejectReclamationReason $rejectReclamationReason
     */
    public function __construct(Role $role, Color $color, Quality $quality, User $user, InvoiceType $invoiceType, Badge $badge, Currency $currency, \App\Models\DeliveryStatus $deliveryStatus, RejectReclamationReason $rejectReclamationReason)
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
    }

    /**
     * Fill database libraries.
     *
     * @return void
     */
    public function fillLibraries()
    {
        $this->role->insert(require database_path('setup/roles.php'));

        $this->color->insert(require database_path('setup/colors.php'));

        $this->quality->insert(require database_path('setup/quality.php'));

        $this->insertInvoiceTypes();

        $this->insertBadges();

        $this->insertCurrencies();

        $this->insertDeliveryStatus();

        $this->insertRejectReclamationReasons();

    }

    /**
     * Create root user.
     *
     * @return void
     */
    public function insertRootUser()
    {
        $this->user->create([
            'name' => 'Nikeddarn',
            'email' => 'nikeddarn@gmail.com',
            'password' => bcrypt('assodance'),
            'roles_id' => $this->role->where('title', 'root')->first()->id,
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

        foreach ((new ReflectionClass(DeliveryStatus::class))->getConstants() as $constantValue) {
            $this->deliveryStatus->create(array_merge(['id' => $constantValue], $statuses[$constantValue]));
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

}