<?php

/**
 * Created by PhpStorm.
 * User: nick
 * Date: 10.12.17
 * Time: 18:17
 */

namespace App\Http\ControllerHelpers\Prices;

use App\Models\Product;
use App\Models\User;

class PriceMaker
{

    /**
     * @var User
     */
    private $user;

    /**
     * PriceMaker constructor.
     * Retrieve user if exist.
     */
    public function __construct()
    {
        $this->user = auth('web')->user();
    }

    public function getPrice(Product $product)
    {

    }
}