<?php
/**
 * Define types of invoices.
 * Define methods to retrieve model and type.
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceTypes
{
    const USER_SALE = 11;

    const USER_RETURN_SALE = 12;

    const VENDOR_PURCHASE = 13;

    const VENDOR_RETURN_PURCHASE = 14;


    const USER_RECLAMATION = 21;

    const USER_EXCHANGE_RECLAMATION = 22;

    const USER_WRITE_OFF_RECLAMATION = 23;

    const VENDOR_RECLAMATION = 24;

    const VENDOR_EXCHANGE_RECLAMATION = 25;

    const VENDOR_WRITE_OFF_RECLAMATION = 26;


    const USER_PAYMENT = 31;

    const USER_RETURN_PAYMENT = 32;

    const VENDOR_PAYMENT = 33;

    const VENDOR_RETURN_PAYMENT = 34;


    function getInvoiceType(): int;

    function getTargetInvoiceModel();
}