<?php

/**
 * Create category breadcrumbs.
 */

namespace App\Breadcrumbs\Traits;

use Illuminate\Support\Collection;

trait ModelBreadcrumbs
{
    /**
     * Create categories breadcrumbs.
     *
     * @param Collection $models
     * @param string|null $prefix
     * @return array
     */
    public function modelBreadcrumbs(Collection $models, string $prefix = null): array
    {
        $targetModel = $models->first();

        return [
            [
                'title' => $targetModel->title,
                'url' => $prefix ? $prefix . '/' . $targetModel->url : $targetModel->url,
            ]
        ];
    }
}