<?php
/**
 * Fill libraries.
 * insert root user.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Badges\ProductBadgesInterface;
use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Delivery\DeliveryTypesInterface;
use App\Contracts\Shop\Delivery\PostServicesInterface;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Reclamations\ReclamationStatusInterface;
use App\Contracts\Shop\Reclamations\RejectReclamationReasons;
use App\Contracts\Shop\Roles\DepartmentTypesInterface;
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
use App\Models\ReclamationStatus;
use App\Models\RejectReclamationReason;
use App\Models\Role;
use App\Models\StorageDepartmentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
     * @var InvoiceStatus
     */
    private $invoiceStatus;
    /**
     * @var ReclamationStatusInterface
     */
    private $reclamationStatus;
    /**
     * @var StorageDepartmentType
     */
    private $storageDepartmentType;

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
     * @param InvoiceStatus $invoiceStatus
     * @param ReclamationStatus $reclamationStatus
     * @param StorageDepartmentType $storageDepartmentType
     */
    public function __construct(Role $role, Color $color, Quality $quality, User $user, InvoiceType $invoiceType, Badge $badge, Currency $currency, DeliveryStatus $deliveryStatus, RejectReclamationReason $rejectReclamationReason, DeliveryType $deliveryType, PostService $postService, InvoiceStatus $invoiceStatus, ReclamationStatus $reclamationStatus, StorageDepartmentType $storageDepartmentType)
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
        $this->invoiceStatus = $invoiceStatus;
        $this->reclamationStatus = $reclamationStatus;
        $this->storageDepartmentType = $storageDepartmentType;
    }

    /**
     * Fill database libraries.
     *
     * @return void
     */
    public function fillLibraries()
    {
        $this->fillTranslatableLibrary($this->color, database_path('setup/colors.php'));

        $this->fillTranslatableLibrary($this->quality, database_path('setup/quality.php'));

        $this->insertCurrencies();

        $this->fillTranslatableConstantsLibrary(InvoiceTypes::class, $this->invoiceType, database_path('setup/invoice_types.php'));

        $this->fillTranslatableConstantsLibrary(InvoiceStatusInterface::class, $this->invoiceStatus, database_path('setup/invoice_status.php'));

        $this->fillTranslatableConstantsLibrary(ProductBadgesInterface::class, $this->badge, database_path('setup/badges.php'));

        $this->fillTranslatableConstantsLibrary(DeliveryStatusInterface::class, $this->deliveryStatus, database_path('setup/delivery_status.php'));

        $this->fillTranslatableConstantsLibrary(DeliveryTypesInterface::class, $this->deliveryType, database_path('setup/delivery_types.php'));

        $this->fillTranslatableConstantsLibrary(PostServicesInterface::class, $this->postService, database_path('setup/post_services.php'));

        $this->fillTranslatableConstantsLibrary(RejectReclamationReasons::class, $this->rejectReclamationReason, database_path('setup/reject_reclamation_reasons.php'));

        $this->fillTranslatableConstantsLibrary(ReclamationStatusInterface::class, $this->reclamationStatus, database_path('setup/reclamation_status.php'));

        $this->fillNotTranslatableConstantsLibrary($this->role, UserRolesInterface::class);

        $this->fillNotTranslatableConstantsLibrary($this->storageDepartmentType, DepartmentTypesInterface::class);
    }

    /**
     * Create root user.
     *
     * @return void
     */
    public function insertRootUser()
    {
        // create root user
        $user = $this->user->create([
            'name' => 'Nikeddarn',
            'email' => 'nikeddarn@gmail.com',
            'password' => bcrypt('assodance'),
        ]);

        // add general attribute to root user parameters
        $userRoleParameters = [
            'general' => 1,
        ];

        // attach roles to root user
        $user->role()->attach([
            UserRolesInterface::ROOT => $userRoleParameters,
            UserRolesInterface::USER_MANAGER => $userRoleParameters,
            UserRolesInterface::VENDOR_MANAGER => $userRoleParameters,
            UserRolesInterface::SERVICEMAN => $userRoleParameters,
            UserRolesInterface::STOREKEEPER => $userRoleParameters,
            UserRolesInterface::OWNER => $userRoleParameters,
        ]);
    }

    /**
     * Fill translatable libraries.
     *
     * @param string $interfaceConstantsClass
     * @param Model $libraryModel
     * @param string $libraryValuePath
     */
    private function fillTranslatableConstantsLibrary(string $interfaceConstantsClass, Model $libraryModel, string $libraryValuePath)
    {
        $types = require $libraryValuePath;

        foreach ((new ReflectionClass($interfaceConstantsClass))->getConstants() as $constantValue) {
            $libraryModel->create(array_merge(['id' => $constantValue], $types[$constantValue]));
        }
    }

    /**
     * Fill not translatable libraries.
     *
     * @param Model $libraryModel
     * @param string $interfaceConstantsClass
     */
    private function fillNotTranslatableConstantsLibrary(Model $libraryModel, string $interfaceConstantsClass)
    {
        foreach ((new ReflectionClass($interfaceConstantsClass))->getConstants() as $constantName => $constantValue) {
            $libraryModel->create([
                'title' => ucfirst(strtolower(str_replace('_', ' ', $constantName))),
                'id' => $constantValue,
            ]);
        }
    }

    /**
     * Fill translatable library.
     *
     * @param Model $libraryModel
     * @param string $libraryValuePath
     */
    private function fillTranslatableLibrary(Model $libraryModel, string $libraryValuePath)
    {
        $libraryModel->create(require $libraryValuePath);
    }

    /**
     * Insert currencies.
     */
    private function insertCurrencies()
    {
        foreach ((new ReflectionClass(CurrenciesInterface::class))->getConstants() as $constantName => $constantValue) {
            $this->currency->create([
                'code' => $constantName,
                'id' => $constantValue,
            ]);
        }
    }
}