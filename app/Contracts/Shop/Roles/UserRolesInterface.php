<?php
/**
 * User roles.
 */

namespace App\Contracts\Shop\Roles;


interface UserRolesInterface
{
    const ROOT = 1;

    const ADMIN = 2;

    const USER_MANAGER = 3;

    const VENDOR_MANAGER = 4;

    const STOREKEEPER = 5;

    const SERVICEMAN = 6;
}