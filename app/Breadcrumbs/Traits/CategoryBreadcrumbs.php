<?php

/**
 * Create category breadcrumbs.
 */

namespace App\Breadcrumbs\Traits;

use Illuminate\Support\Collection;

trait CategoryBreadcrumbs
{
    /**
     * Create categories breadcrumbs.
     *
     * @param Collection $categories
     * @param string|null $prefix
     * @return array
     */
    public function categoryBreadcrumbs(Collection $categories, string $prefix = null): array
    {
        $breadcrumbs = [];

        foreach ($categories->sortBy('depth') as $category){
            $breadcrumbs[] = [
                'title' => $category->title,
                'url' => $prefix ? $prefix . '/' . $category->url : $category->url,
            ];
        }

        return $breadcrumbs;
    }
}