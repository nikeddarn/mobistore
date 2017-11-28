<?php

/**
 * Receive array of items that contains 'title' and 'url' properties.
 * Create array of breadcrumbs that contains 'title' and 'url' keys.
 */

namespace App\Contracts\Breadcrumbs;

interface BreadcrumbsCreatorInterface
{
    /**
     * Create array of breadcrumbs that contains 'title' and 'url' keys.
     *
     * @param array $items
     * @return array
     */
    public function createBreadcrumbs(array $items): array;
}