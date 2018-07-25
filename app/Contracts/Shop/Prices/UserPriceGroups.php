<?php
/**
 * User price groups.
 */

namespace App\Contracts\Shop\Prices;


interface UserPriceGroups
{
    const RETAIL = 1;

    const REGULAR_RETAIL = 2;

    const WHOLESALE = 3;

    const LARGE_WHOLESALE = 4;

    const EXCLUSIVE = 5;
}