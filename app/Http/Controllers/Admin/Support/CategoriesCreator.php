<?php
/**
 * Create categories, brands and models.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\MetaData;

class CategoriesCreator
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
     * CategoriesCreator constructor.
     *
     * @param Category $category
     * @param Brand $brand
     * @param DeviceModel $model
     * @param MetaData $metaData
     */
    public function __construct(Category $category, Brand $brand, DeviceModel $model, MetaData $metaData)
    {

        $this->category = $category;
        $this->brand = $brand;
        $this->model = $model;
        $this->metaData = $metaData;
    }
    /**
     * Insert categories.
     *
     * @return void
     */
    public function insertCategories()
    {
        $categories = require database_path('setup/categories.php');

        $this->category->create($categories);

        $metaDataFiller = function ($categories) use (&$metaDataFiller) {
            foreach ($categories as $category) {

                $metaData = $this->metaData->create($category);
                usleep(1000);
                $metaData->categories_id = $this->category->where('breadcrumb', $category['breadcrumb'])->first()->id;
                $metaData->save();
                usleep(1000);

                if (isset($category['children'])) {
                    $metaDataFiller($category['children']);
                }
            }
        };

        $metaDataFiller([$categories]);
    }

    /**
     * Insert brands.
     *
     * @return void
     */
    public function insertBrands()
    {
        foreach (require database_path('setup/brands.php') as $brand) {

            $brandId = $this->brand->create($brand)->id;

            $metaData = $this->metaData->create($brand);
            $metaData->brands_id = $brandId;
            $metaData->save();
        }
    }

    /**
     * Insert models.
     *
     * @return void
     */
    public function insertModels()
    {
        foreach (require database_path('setup/models.php') as $brand => $modelsBySeries) {

            $brandId = $this->brand->where('title', $brand)->first()->id;

            foreach ($modelsBySeries as $series => $models) {

                foreach ($models as $model) {
                    $deviceModel = $this->model->create($model);
                    $deviceModel->brands_id = $brandId;
                    $deviceModel->series = $series;
                    $deviceModel->save();

                    $metaData = $this->metaData->create($model);
                    $metaData->brands_id = $brandId;
                    $metaData->models_id = $deviceModel->id;
                    $metaData->save();
                }

            }
        }
    }
}