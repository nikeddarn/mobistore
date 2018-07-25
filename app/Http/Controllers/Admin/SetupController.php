<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Shop\Roles\DepartmentTypesInterface;
use App\Http\Controllers\Admin\Support\CategoriesCreator;
use App\Http\Controllers\Admin\Support\CommonMetaData;
use App\Http\Controllers\Admin\Support\ImageCreator;
use App\Http\Controllers\Admin\Support\InitializeApplication;
use App\Http\Controllers\Admin\Support\ProductCreator;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Storage;
use App\Models\Vendor;
use ReflectionClass;

class SetupController extends Controller
{
    /**
     * @var InitializeApplication
     */
    private $initializer;

    /**
     * @var CommonMetaData
     */
    private $metaData;

    /**
     * @var CategoriesCreator
     */
    private $categoriesCreator;

    /**
     * @var ProductCreator
     */
    private $productCreator;

    /**
     * @var ImageCreator
     */
    private $imageCreator;
    /**
     * @var Vendor
     */
    private $vendor;
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Currency
     */
    private $currency;


    /**
     * SetupController constructor.
     *
     * @param InitializeApplication $initializer
     * @param CommonMetaData $metaData
     * @param CategoriesCreator $categoriesCreator
     * @param ProductCreator $productCreator
     * @param ImageCreator $imageCreator
     * @param Vendor $vendor
     * @param Storage $storage
     * @param Currency $currency
     */
    public function __construct(InitializeApplication $initializer, CommonMetaData $metaData, CategoriesCreator $categoriesCreator, ProductCreator $productCreator, ImageCreator $imageCreator, Vendor $vendor, Storage $storage, Currency $currency)
    {
        $this->initializer = $initializer;
        $this->metaData = $metaData;
        $this->categoriesCreator = $categoriesCreator;
        $this->productCreator = $productCreator;
        $this->imageCreator = $imageCreator;
        $this->vendor = $vendor;
        $this->storage = $storage;
        $this->currency = $currency;
    }

    /**
     * Make full setup.
     */
    public function setup()
    {
        $this->initialize();


        $this->categoriesBrandsModels();
        $this->commonMetaData();
        $this->products();
//        $this->watermark();
        $this->cities();
        $this->vendors();
        $this->createStorages();

        return view('content.admin.setup.message')->with([
            'setup_message' => 'Application was created.',
        ]);
    }

    /**
     * Fill libraries. Create root user.
     */
    public function initialize()
    {
        $this->initializer->fillLibraries();
        $this->initializer->insertRootUser();
    }

    /**
     * Create  categories, brands and models.
     */
    private function categoriesBrandsModels()
    {
        $this->categoriesCreator->insertCategories();
        $this->categoriesCreator->insertBrands();
        $this->categoriesCreator->insertModels();
    }

    /**
     * Insert common meta data.
     */
    private function commonMetaData()
    {
        $this->metaData->insertCommonMetadata();
    }

    /**
     * Add products.
     */
    private function products()
    {
        $this->productCreator->insertProducts();
    }

    /**
     * Make watermark.
     * Make smaller images.
     *
     */
    public function watermark()
    {
        $this->imageCreator->watermark();
    }

    /*
     * Insert vendors.
     */
    private function vendors()
    {
        foreach (require database_path('setup/vendors.php') as $vendor){
            $this->vendor->create($vendor);
        }
    }

    /*
     * Insert cities.
     */
    private function cities()
    {
        foreach (require database_path('setup/cities.php') as $city){
            $this->storage->create($city);
        }
    }

    /*
     * Create storages and it's departments.
     */
    private function createStorages()
    {
        // add preset storages
        foreach (require database_path('setup/storages.php') as $storage){
            $storage = $this->storage->create($storage);

            // add storage departments
            foreach ((new ReflectionClass(DepartmentTypesInterface::class))->getConstants() as $constantValue){
                $storage->storageDepartment()->create([
                    'storage_department_types_id' => $constantValue,
                ]);
            }
        }
    }
}