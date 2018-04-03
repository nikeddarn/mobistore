<?php
/**
 * Multi vendor products routing
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Vendor;
use Illuminate\Support\Collection;

class VendorProductRouter
{
    /**
     * @var Vendor
     */
    private $vendor;


    /**
     * StorageProductRouter constructor.
     *
     * @param Vendor $vendor
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Get vendor id that will serve given invoice.
     *
     * @param Collection $invoiceProducts
     * @return int
     */
//    public function defineInvoiceVendor(Collection $invoiceProducts):int
//    {
//        if ($this->vendor->count() === 1){
//            return $this->vendor->first()->id;
//        }else{
//            return $this->vendor->first()->id;
//        }
//    }
}