<?php
/**
 * Fill libraries.
 * insert root user.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\Color;
use App\Models\InvoiceType;
use App\Models\Quality;
use App\Models\Role;
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
     * @var Vendor
     */
    private $vendor;
    /**
     * @var User
     */
    private $user;

    /**
     * @var InvoiceType
     */
    private $invoiceType;

    /**
     * InitializeApplication constructor.
     * @param Role $role
     * @param Color $color
     * @param Quality $quality
     * @param Vendor $vendor
     * @param User $user
     * @param InvoiceType $invoiceType
     */
    public function __construct(Role $role, Color $color, Quality $quality, Vendor $vendor, User $user, InvoiceType $invoiceType)
    {

        $this->role = $role;
        $this->color = $color;
        $this->quality = $quality;
        $this->vendor = $vendor;
        $this->user = $user;
        $this->invoiceType = $invoiceType;
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

        $this->vendor->insert(require database_path('setup/vendors.php'));

        $this->insertInvoiceTypes();

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
}