<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Support\CategoriesCreator;
use App\Http\Controllers\Admin\Support\CommonMetaData;
use App\Http\Controllers\Admin\Support\ImageCreator;
use App\Http\Controllers\Admin\Support\InitializeApplication;
use App\Http\Controllers\Admin\Support\ProductCreator;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Storage;
use App\Models\Vendor;
use App\Models\VendorProduct;

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
        $message = $this->initialize();
        $message .= $this->categoriesBrandsModels();
        $message .= $this->commonMetaData();
        $message .= $this->products();
//        $message .= $this->watermark();
        $message .= $this->cities();
        $message .= $this->vendors();
        $message .= $this->storages();

        return view('content.admin.setup.message')->with([
            'setup_message' => $message,
        ]);
    }

//    public function insert()
//    {
//        VendorProduct::create([
//            [
//                'vendors_id' => 1,
//                'vendor_product_id' => '22',
//                'products_id' => 3,
//                'stock_quantity' => 10,
//                'delivery_price' => 2,
//                'offer_price' => 23
//            ],
//            [
//                'vendors_id' => 1,
//                'vendor_product_id' => '22',
//                'products_id' => 4,
//                'stock_quantity' => 10,
//                'delivery_price' => 2,
//                'offer_price' => 24
//            ],
//            [
//                'vendors_id' => 1,
//                'vendor_product_id' => '22',
//                'products_id' => 5,
//                'stock_quantity' => 10,
//                'delivery_price' => 2,
//                'offer_price' => 27
//            ],
//        ]);
//    }

    /**
     * Fill libraries. Create root.
     *
     * @return string
     */
    public function initialize()
    {
        $this->initializer->fillLibraries();
        $this->initializer->insertRootUser();

        return '<p>Database libraries was filled.</p><p>Root user was created</p><p><strong class="alert-danger">Do not forget to destroy this route!!</strong></p>';
    }

    /**
     * Create  categories, brands and models.
     *
     * @return string
     */
    private function categoriesBrandsModels()
    {
        $this->categoriesCreator->insertCategories();
        $this->categoriesCreator->insertBrands();
        $this->categoriesCreator->insertModels();
        return '<p>Categories was inserted.</p><p>Brands was inserted.</p><p>Models was inserted.</p>';
    }

    /**
     * Insert common meta data.
     *
     * @return string
     */
    private function commonMetaData()
    {
        $this->metaData->insertCommonMetadata();
        return '<p>Meta data was inserted.</p>';
    }

    /**
     * Add products.
     *
     * @return string
     */
    private function products()
    {
        $this->productCreator->insertProducts();
        return '<p>Products was inserted.</p>';
    }

    /**
     * Make watermark.
     * Make smaller images.
     *
     * @return string
     */
    public function watermark()
    {
        $this->imageCreator->watermark();
        return '<p>Images was watermarked.</p>';
    }

    /*
     * Insert vendors.
     */
    private function vendors()
    {
        foreach (require database_path('setup/vendors.php') as $vendor){
            $this->vendor->create($vendor);
        }

        return '<p>Vendors was inserted.</p>';
    }

    /*
     * Insert cities.
     */
    private function cities()
    {
        foreach (require database_path('setup/cities.php') as $city){
            $this->storage->create($city);
        }

        return '<p>Cities was inserted.</p>';
    }

    /*
     * Insert storages.
     */
    private function storages()
    {
        foreach (require database_path('setup/storages.php') as $storage){
            $this->storage->create($storage);
        }

        return '<p>Storages was inserted.</p>';
    }

}