<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 13.12.17
 * Time: 21:01
 */

namespace App\Contracts\Shop\Badges;


interface BadgeTypes
{
    const NEW = 1;
    const PRICE_DOWN = 2;
    const ENDING = 3;
    const SALE = 4;
    const BEST_SELLER = 5;

}