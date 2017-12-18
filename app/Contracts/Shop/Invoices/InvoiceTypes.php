<?php
/**
 * Define types of invoices.
 * Define methods to retrieve model and type.
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceTypes
{
    const BASKET = 11;

    const VENDOR_PRE_ORDER = 12;

    const DELIVERY = 13;


    const ORDER = 21;

    const RETURN_ORDER =22;


    const RECLAMATION = 31;

    const EXCHANGE_RECLAMATION = 32;

    const WRITE_OFF_RECLAMATION = 33;


    const PAYMENT = 41;

    const RETURN_PAYMENT = 42;


    function getInvoiceType(): int;

    function getTargetInvoiceModel();
}