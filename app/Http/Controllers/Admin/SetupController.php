<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductModelCompatible;
use App\Models\Quality;
use App\Models\Role;
use App\Models\User;
use App\Models\Color;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SetupController extends Controller
{
    /**
     * Fill libraries. Create root.
     */
    public function setup()
    {
        $this->fillLibraries();
        $this->insertRootUser();

        return view('content.admin.setup.message')->with($this->successMessage());
    }

    /**
     * Create new categories or show form to confirm categories recreation if exist.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        if (config('app.env') === 'production' && (Category::all()->count() || Brand::all()->count())) {
            return view('content.admin.setup.setup_confirmation')->with('message', 'All data will be destroyed. Proceed anyway?');
        } else {
            $this->fillDatabase();
            return view('content.admin.setup.message')->with($this->successMessage());
        }

    }

    /**
     * Add products.
     */
    public function products()
    {
        if (config('app.env') === 'local') {
            DB::statement("DELETE FROM `vendors_has_products`");
            DB::statement("DELETE FROM `product_images`");
            DB::statement("DELETE FROM `products`");
        }
        $this->addProducts();
        return view('content.admin.setup.message')->with($this->successMessage());
    }

    public function watermark()
    {
        $text=config('app.url');
        $font = public_path('fonts/OpenSans-Regular.ttf');
        foreach (Storage::disk('local')->allFiles('images/products/raw') as $file){
            $imagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $file;
            $imageSize = getimagesize($imagePath);
            $textLeft = $imageSize[0] * 0.1;
            $textTop = $imageSize[1] * 0.8;
            $textSize = $imageSize[0] * 0.08;
            $image = imagecreatefromjpeg($imagePath);
            if($imageSize[0] > $imageSize[1]){
                $image = imagerotate($image, 90, 0);
            }
            $color = 0x50317EAC;
            imagettftext($image, $textSize, 0, $textLeft, $textTop, $color, $font, $text);
            $f = fopen('php://memory', 'w+');
            imagejpeg($image, $f);
            Storage::disk('public')->put(str_replace('raw/', '', $file), $f);
        }
    }

    private function fillLibraries()
    {
        DB::table('roles')->delete();
        Role::insert(require database_path('setup/roles.php'));

        DB::table('colors')->delete();
        Color::insert(require database_path('setup/colors.php'));

        DB::table('quality')->delete();
        Quality::insert(require database_path('setup/quality.php'));

        DB::table('vendors_has_products')->delete();
        DB::table('vendors')->delete();
        Vendor::insert(require database_path('setup/vendors.php'));
    }

    private function insertRootUser()
    {
        User::create([
            'name' => 'Nikeddarn',
            'email' => 'nikeddarn@gmail.com',
            'password' => bcrypt('assodance'),
            'roles_id' => Role::where('title', 'root')->first()->id,
        ]);
    }

    /**
     * Create new categories if request is checked.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function confirmSetup(Request $request)
    {
        if ($request->get('setup_confirmation')) {
            $this->fillDatabase();
            return view('content.admin.setup.message')->with($this->successMessage());
        } else {
            return view('content.admin.setup.message')->with($this->declineMessage());
        }
    }

    /**
     * Destroy old data.
     * Fill in database new data.
     *
     * @return void
     */
    private function fillDatabase()
    {
        $this->destroyOldData();
        $this->buildCategories();
        $this->buildBrands();
        $this->buildModels();
    }

    /**
     * Create success message.
     *
     * @return array
     */
    private function successMessage()
    {
        return [
            'setup_message' => 'Database was successfully filled.'
        ];
    }

    /**
     * Create decline message.
     *
     * @return array
     */
    private function declineMessage()
    {
        return [
            'setup_message' => 'Your request was declined.'
        ];
    }

    private function destroyOldData()
    {
        DB::statement("DELETE FROM `products`");
        DB::statement("DELETE FROM `brands`");
        DB::statement("DELETE FROM `categories`");
        DB::statement("DELETE FROM `models`");
        DB::statement("DELETE FROM `meta_data`");
    }

    /**
     * Create tree of categories.
     * Insert categories data into `meta_data`.
     */
    private function buildCategories()
    {
        $categories = require database_path('setup/categories.php');

        Category::create($categories);

        $metaDataFiller = function ($categories) use (&$metaDataFiller) {
            foreach ($categories as $category) {

                $metaData = MetaData::create($category);

                $metaData->categories_id = Category::where('breadcrumb', $category['breadcrumb'])->first()->id;
                $metaData->save();

                if (isset($category['children'])) {
                    $metaDataFiller($category['children']);
                }
            }
        };

        $metaDataFiller([$categories]);
    }

    /**
     * Create list of brands.
     *
     * @return void
     */
    private function buildBrands()
    {
        $brands = require database_path('setup/brands.php');
        foreach ($brands as $brand) {
            $brandId = Brand::create($brand)->id;

            $metaData = MetaData::create($brand);
            $metaData->brands_id = $brandId;
            $metaData->save();
        }
    }

    /**
     * Create list of brands.
     *
     * @return void
     */
    private function buildModels()
    {
        $modelsByBrands = require database_path('setup/models.php');

        foreach ($modelsByBrands as $brand => $modelsBySeries) {
            $brandId = Brand::where('title', $brand)->first()->id;
            foreach ($modelsBySeries as $series => $models) {
                foreach ($models as $model) {
                    $deviceModel = DeviceModel::create($model);
                    $deviceModel->brands_id = $brandId;
                    $deviceModel->series = $series;
                    $deviceModel->save();

                    $metaData = MetaData::create($model);
                    $metaData->brands_id = $brandId;
                    $metaData->models_id = $deviceModel->id;
                    $metaData->save();
                }
            }
        }
    }

    private function addProducts()
    {
        foreach (require database_path('setup/products.php') as $item) {
            $category = Category::where('title_en', $item['category'])->first();
            $brand = Brand::where('title', $item['brand'])->first();
            $models = DeviceModel::whereIn('title', $item['models'])->get();
            $quality = Quality::where('title_en', $item['quality'])->first();
            $item['categories_id'] = $category->id;
            $item['brands_id'] = $brand->id;
            $item['quality_id'] = $quality->id;
            if (config('app.env') === 'local' || !Product::where('url', $item['url'])->count()) {
                $product = Product::create($item);
                foreach ($models as $model) {
                    ProductModelCompatible::create(['products_id' => $product->id, 'models_id' => $model->id]);
                }
                usleep(1000);
                $this->addCrossLinks($category, $brand, $models);
                usleep(1000);
            }
            if (isset($item['images']) && is_array($item['images'])) {
                foreach ($item['images'] as $image) {
                    $this->copyImage($image['url']);
                    ProductImage::create(['products_id' => $product->id, 'image' => $image['url'], 'is_primary' => isset($image['is_primary']) ? 1 : 0]);
                }
            }
            if (isset($item['vendors']) && is_array($item['vendors'])) {
                foreach ($item['vendors'] as $vendorTitle => $vendorData) {
                    VendorProduct::create(['vendors_id' => Vendor::where('title', $vendorTitle)->first()->id, 'products_id' => $product->id, 'vendor_product_id' => $vendorData['code']]);
                }
            }
        }
    }

    private function copyImage($url)
    {
        $inputStream = Storage::disk('setup')->getDriver()->readStream($url);
        $destination = 'images/products/raw/' . $url;
        Storage::disk('local')->getDriver()->putStream($destination, $inputStream);
    }

    private function addCrossLinks($category, $brand, $models)
    {
        // forward links
        $metaData = [];
        $metaData['brands_id'] = $brand->id;
        $metaData['categories_id'] = $category->id;

        $metaData['models_id'] = null;
        $metaData['url'] = $category->url . '/' . $brand->url;
        MetaData::updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($category, $brand)));

        foreach ($models as $model) {
            $metaData['models_id'] = $model->id;
            $metaData['url'] = $category->url . '/' . $model->url;
            MetaData::updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($category, $brand, $model)));
        }

        // revers links
        $metaData = [];
        $metaData['brands_id'] = $brand->id;
        foreach ($models as $model) {
            $metaData['models_id'] = $model->id;
            foreach (Category::withDepth()->ancestorsAndSelf($category->id) as $ancestor) {
                if (!($ancestor->depth === 0)) {
                    $metaData['categories_id'] = $ancestor->id;
                    $metaData['url'] = $model->url . '/' . $ancestor->url;
                    if ($ancestor->isLeaf()) {
                        $metaData['is_canonical'] = 0;
                    }
                    MetaData::updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($ancestor, $brand, $model)));
                }
            }
        }
    }

    private function setMetaDataProperties($category, $brand, $model = null)
    {
        $bue_en = trans('meta.phrases.bue', [], 'en');
        $features_en = trans('meta.phrases.features', [], 'en');
        $wholesaleRetail_en = trans('meta.phrases.wholesale_and_retail', [], 'en');
//        $originalCopy_en = trans('meta.phrases.original_and_copy', [], 'en');

        $bue_ru = trans('meta.phrases.bue', [], 'ru');
        $features_ru = trans('meta.phrases.features', [], 'ru');
        $wholesaleRetail_ru = trans('meta.phrases.wholesale_and_retail', [], 'ru');
//        $originalCopy_ru = trans('meta.phrases.original_and_copy', [], 'ru');

        $bue_ua = trans('meta.phrases.bue', [], 'ua');
        $features_ua = trans('meta.phrases.features', [], 'ua');
        $wholesaleRetail_ua = trans('meta.phrases.wholesale_and_retail', [], 'ua');
//        $originalCopy_ua = trans('meta.phrases.original_and_copy', [], 'ua');

        return [
            'page_title_en' => $category->title_en . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : ''),
            'page_title_ru' => $category->title_ru . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : ''),
            'page_title_ua' => $category->title_ua . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : ''),
            'meta_title_en' => $category->title_en . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . ' ' . $wholesaleRetail_en,
            'meta_title_ru' => $category->title_ru . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . ' ' . $wholesaleRetail_ru,
            'meta_title_ua' => $category->title_ua . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . ' ' . $wholesaleRetail_ua,
            'meta_description_en' => $category->title_en . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en . '.',
            'meta_description_ru' => $category->title_ru . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru . '.',
            'meta_description_ua' => $category->title_ua . ' ' . $brand->title . (isset($model) ? ' ' . $model->title : '') . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua . '.',
            'meta_keywords_en' => $category->meta_keywords_en . ', ' . $brand->meta_keywords_en . (isset($model) ? ', ' . $model->meta_keywords_en : ''),
            'meta_keywords_ru' => $category->meta_keywords_ru . ', ' . $brand->meta_keywords_ru . (isset($model) ? ', ' . $model->meta_keywords_ru : ''),
            'meta_keywords_ua' => $category->meta_keywords_ua . ', ' . $brand->meta_keywords_ua . (isset($model) ? ', ' . $model->meta_keywords_ua : ''),

        ];
    }
}