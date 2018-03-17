<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 11.03.18
 * Time: 19:37
 */

namespace App\Contracts\Channels;


interface SmsTypesInterface
{
    const SIMPLE = 0;

    const FLASH = 1;

    const WAP_PUSH = 2;
}