<?php

/**
 * Class contains set of methods that make instance of creator and call its method 'createBreadcrumbs'.
 */

namespace App\Breadcrumbs;

class BreadcrumbsCreator
{
    /**
     * Make creator instance. Create breadcrumbs.
     *
     * @param array $breadcrumbsItems
     * @return array
     */
    public function createBrandRouteBreadcrumbs(array $breadcrumbsItems):array
    {
        return (new BrandRouteBreadcrumbsCreator())->createBreadcrumbs($breadcrumbsItems);
    }

    /**
     * Make creator instance. Create breadcrumbs.
     *
     * @param array $breadcrumbsItems
     * @return array
     */
    public function createCategoryRouteBreadcrumbs(array $breadcrumbsItems):array
    {
        return (new CategoryRouteBreadcrumbsCreator())->createBreadcrumbs($breadcrumbsItems);
    }
}