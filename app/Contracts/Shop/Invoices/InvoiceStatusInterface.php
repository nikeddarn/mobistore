<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 14.03.18
 * Time: 20:35
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceStatusInterface
{
    const PROCESSING = 1;

    const FINISHED = 2;

    const CANCELLED = 3;
}