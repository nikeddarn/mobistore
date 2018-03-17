<?php
/**
 * Define types of invoices.
 * Define methods to retrieve model and type.
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceTypes
{
    const CART = 11;


    const ORDER = 21;

    const PRE_ORDER = 22;

    const RETURN_ORDER =23;


    const RECLAMATION = 31;

    const EXCHANGE_RECLAMATION = 32;

    const RETURN_RECLAMATION = 33;

    const WRITE_OFF_RECLAMATION = 34;


    const PAYMENT = 41;

    const RETURN_PAYMENT = 42;


    const STORAGE_REPLACEMENT = 51;
}
