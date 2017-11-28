<?php

/**
 * Create category breadcrumbs.
 */

namespace App\Breadcrumbs\Traits;

use Illuminate\Support\Collection;

trait BrandBreadcrumbs
{
    /**
     * Create categories breadcrumbs.
     *
     * @param Collection $brands
     * @param string|null $prefix
     * @return array
     */
    public function brandBreadcrumbs(Collection $brands, string $prefix = null): array
    {
        $breadcrumbs = [];

        // add 'root' brand breadcrumb from given MetaData model
        if ($brands->count() === 2) {
            $breadcrumbs[] = [
                'title' => $brands->first()->page_title,
                'url' => $prefix ? $prefix : '',
            ];
        }

        // add selected brand breadcrumb
        $breadcrumbs[] = [
            'title' => $brands->last()->title,
            'url' => $prefix ? $prefix . '/' . $brands->last()->url : $brands->last()->url,
        ];

        return $breadcrumbs;
    }
}