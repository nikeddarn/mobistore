<?php
/**
 * Fill libraries.
 * insert root user.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Badges\BadgeTypes;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\Badge;
use App\Models\Color;
use App\Models\Currency;
use App\Models\InvoiceType;
use App\Models\Quality;
use App\Models\Role;
use App\Models\Storage;
use App\Models\User;
use App\Models\Vendor;
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
     * InitializeApplication constructor.
     * @param Role $role
     * @param Color $color
     * @param Quality $quality
     * @param User $user
     * @param InvoiceType $invoiceType
     * @param Badge $badge
     * @param Currency $currency
     */
    public function __construct(Role $role, Color $color, Quality $quality, User $user, InvoiceType $invoiceType, Badge $badge, Currency $currency)
    {

        $this->role = $role;
        $this->color = $color;
        $this->quality = $quality;
        $this->user = $user;
        $this->invoiceType = $invoiceType;
        $this->badge = $badge;
        $this->currency = $currency;
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
        $types = [];

        foreach ((new ReflectionClass(InvoiceTypes::class))->getConstants() as $constant => $value) {
            $types[] = [
                'id' => $value,
                'title' => $constant,
            ];
        }

        $this->invoiceType->insert($types);
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
}