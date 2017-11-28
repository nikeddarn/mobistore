<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 26.11.17
 * Time: 18:18
 */

namespace App\Breadcrumbs;


use App\Breadcrumbs\Traits\BrandBreadcrumbs;
use App\Breadcrumbs\Traits\CategoryBreadcrumbs;
use App\Breadcrumbs\Traits\ModelBreadcrumbs;
use App\Contracts\Breadcrumbs\BreadcrumbsCreatorInterface;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use Exception;

class BrandRouteBreadcrumbsCreator implements BreadcrumbsCreatorInterface, FilterTypes
{
    use CategoryBreadcrumbs;
    use BrandBreadcrumbs;
    use ModelBreadcrumbs;

    /**
     * Create array of breadcrumbs that contains 'title' and 'url' keys.
     *
     * @param array $items
     * @return array
     * @throws Exception
     */
    public function createBreadcrumbs(array $items): array
    {
        $breadcrumbs = [];

        if (isset($items[self::BRAND]) && $items[self::BRAND]->count()) {
            $breadcrumbs[] = $this->brandBreadcrumbs($items[self::BRAND], '/brand');
        } else {
            throw new Exception('Collection must contains root brand (MetaData model with url="brand")');
        }

        if (isset($items[self::MODEL]) && $items[self::MODEL]->count() === 1) {
            $breadcrumbs[] = $this->modelBreadcrumbs($items[self::MODEL], '/brand');

            if (isset($items[self::CATEGORY]) && $items[self::CATEGORY]->count()) {
                $breadcrumbs[] = $this->categoryBreadcrumbs($items[self::CATEGORY], '/brand/' . $items[self::MODEL]->first()->url);
            }
        }

        return call_user_func_array('array_merge', $breadcrumbs);
    }
}