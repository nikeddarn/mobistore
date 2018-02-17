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
    const ACTION = 3;
    const ENDING = 4;
}