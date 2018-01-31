<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 28.01.18
 * Time: 17:09
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceDirections
{
    const OUTGOING = 'out';

    const INCOMING = 'in';
}