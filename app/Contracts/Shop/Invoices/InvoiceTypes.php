<?php
/**
 * Define types of invoices.
 * Define methods to retrieve model and type.
 */

namespace App\Contracts\Shop\Invoices;


interface InvoiceTypes
{
    // ------------------------------------cart invoices -----------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------

    // cart product invoice
    const USER_CART = 111;

    // ------------------------------------product invoices --------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------

    // order invoices
    const USER_ORDER = 211;

    const USER_PRE_ORDER = 212;

    const VENDOR_ORDER = 213;

    // return order invoices
    const USER_RETURN_ORDER =214;

    const VENDOR_RETURN_ORDER =215;

    // product replacement invoice
    const STORAGE_REMOVE_PRODUCT = 216;


    // ---------------------- product and reclamation invoices ----------------------------------------------
    // ------------------------------------------------------------------------------------------------------

    // replace new product from company's storage department to company's reclamation department
    const REMOVE_PRODUCT_TO_RECLAMATION = 311;

    // replace new product from company's storage department to company's reclamation department
    const REMOVE_RECLAMATION_TO_PRODUCT = 312;

    // replace new product from company's storage department to user's reclamation department
    const USER_EXCHANGE_RECLAMATION = 313;

    // replace new product from vendor's reclamation department to company's storage department
    const VENDOR_EXCHANGE_RECLAMATION = 314;

    // ---------------------- reclamation invoices ----------------------------------------------------------
    // ------------------------------------------------------------------------------------------------------

    // replace defect product from user's reclamation department to company's reclamation department
    const USER_RECLAMATION = 411;

    // replace defect product from company's reclamation department to vendor's reclamation department
    const VENDOR_RECLAMATION = 412;

    // replace defect product from  company's reclamation department to user's reclamation department
    const USER_RETURN_RECLAMATION = 413;

    // replace defect product from vendor's reclamation department to company's reclamation department
    const VENDOR_RETURN_RECLAMATION = 414;

    // replace defect product from one to another company's reclamation department
    const STORAGE_REMOVE_RECLAMATION = 415;

    // user write off reclamation
    const USER_WRITE_OFF_RECLAMATION = 416;

    // vendor write off reclamation
    const VENDOR_WRITE_OFF_RECLAMATION = 417;



    // ---------------------------------------payment invoices ---------------------------------------------
    // -----------------------------------------------------------------------------------------------------

    // user payment
    const USER_PAYMENT = 511;

    // vendor payment
    const VENDOR_PAYMENT = 512;

    // user return payment
    const USER_RETURN_PAYMENT = 513;

    // vendor return payment
    const VENDOR_RETURN_PAYMENT = 514;

    // cash replacement
    const STORAGE_REMOVE_CASH = 515;

    // ---------------------------------------cost invoices -------------------------------------------------
    // ------------------------------------------------------------------------------------------------------

    // ---------- vendor outgoing invoices ------------------

    // cost for delivery from vendor to shipment to user
    const VENDOR_DELIVERY_TO_SHIPMENT_PAYMENT = 611;
}
