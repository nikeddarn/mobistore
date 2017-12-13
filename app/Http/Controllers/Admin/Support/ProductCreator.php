<?php
/**
 * Add products.
 * Add forward and reverse links to product.
 * Add meta data.
 * Copy product images.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DeviceModel;
use App\Models\MetaData;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductModelCompatible;
use App\Models\Quality;
use Illuminate\Support\Facades\Storage;

class ProductCreator
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * @var DeviceModel
     */
    private $model;

    /**
     * @var MetaData
     */
    private $metaData;

    /**
     * @var ProductModelCompatible
     */
    private $productModelCompatible;

    /**
     * @var Color
     */
    private $color;

    /**
     * @var Quality
     */
    private $quality;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductImage
     */
    private $productImage;

    /**
     * ProductCreator constructor.
     * @param Category $category
     * @param Brand $brand
     * @param DeviceModel $model
     * @param MetaData $metaData
     * @param ProductModelCompatible $productModelCompatible
     * @param Color $color
     * @param Quality $quality
     * @param Product $product
     * @param ProductImage $productImage
     */
    public function __construct(Category $category, Brand $brand, DeviceModel $model, MetaData $metaData, ProductModelCompatible $productModelCompatible, Color $color, Quality $quality, Product $product, ProductImage $productImage)
    {

        $this->category = $category;
        $this->brand = $brand;
        $this->model = $model;
        $this->metaData = $metaData;
        $this->productModelCompatible = $productModelCompatible;
        $this->color = $color;
        $this->quality = $quality;
        $this->product = $product;
        $this->productImage = $productImage;
    }
    /**
     * Insert products
     */
    public function insertProducts()
    {
        foreach (require database_path('setup/products.php') as $item) {
            $category = $this->category->where('title_en', $item['category'])->first();
            $brand = $this->brand->where('title', $item['brand'])->first();
            $models = $this->model->whereIn('title', $item['models'])->get();
            $quality = $this->quality->where('title_en', $item['quality'])->first();
            $color = $item['color'] ? $this->color->where('title_en', $item['color'])->first() : null;
            $item['categories_id'] = $category->id;
            $item['brands_id'] = $brand->id;
            $item['quality_id'] = $quality->id;
            $item['colors_id'] = $color ? $color->id : null;
            if (!$this->product->where('url', $item['url'])->count()) {
                $product = $this->product->create($item);
                foreach ($models as $model) {
                    $this->productModelCompatible->create(['products_id' => $product->id, 'models_id' => $model->id]);
                }
                usleep(1000);
                $this->addCrossLinks($category, $brand, $models);
                usleep(1000);

                if (isset($item['images']) && is_array($item['images'])) {
                    foreach ($item['images'] as $image) {
                        $this->copyImage($image['url']);
                        $this->productImage->create(['products_id' => $product->id, 'image' => $image['url'], 'is_primary' => isset($image['is_primary']) ? 1 : 0]);
                    }
                }
//                if (isset($item['vendors']) && is_array($item['vendors'])) {
//                    foreach ($item['vendors'] as $vendorTitle => $vendorData) {
//                        VendorProduct::create(['vendors_id' => Vendor::where('title', $vendorTitle)->first()->id, 'products_id' => $product->id, 'vendor_product_id' => $vendorData['code']]);
//                    }
//                }
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
        $this->metaData->updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($category, $brand)));

        foreach ($models as $model) {
            $metaData['models_id'] = $model->id;
            $metaData['url'] = $category->url . '/' . $model->url;
            $this->metaData->updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($category, $brand, $model)));
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
                    $this->metaData->updateOrCreate(array_merge($metaData, $this->setMetaDataProperties($ancestor, $brand, $model)));
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