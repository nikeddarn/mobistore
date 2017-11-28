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

class CategoryRouteBreadcrumbsCreator implements BreadcrumbsCreatorInterface, FilterTypes
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

        if (isset($items[self::CATEGORY]) && $items[self::CATEGORY]->count()) {
            $breadcrumbs[] = $this->categoryBreadcrumbs($items[self::CATEGORY], '/category');
        }else{
            throw new Exception('Collection must contains root category');
        }

        if (isset($items[self::BRAND]) && $items[self::BRAND]->count()) {
            $urlPrefix = '/category/' . $items[self::CATEGORY]->sortBy('depth')->last()->url;

            $breadcrumbs[] = $this->brandBreadcrumbs($items[self::BRAND], $urlPrefix);

            if (isset($items[self::MODEL]) && $items[self::MODEL]->count() === 1) {
                $breadcrumbs[] = $this->modelBreadcrumbs($items[self::MODEL], $urlPrefix);
            }
        }

        return call_user_func_array('array_merge', $breadcrumbs);
    }
}