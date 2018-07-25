<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * @var Vendor
     */
    private $vendor;

    /**
     * VendorController constructor.
     *
     * @param Vendor $vendor
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Show list of vendors with count of not handled invoices
     *
     * @return View
     */
    public function index()
    {
        return view('content.vendor.list.index')
            ->with('vendors', $this->getVendors());
    }

    /**
     * Get vendors with unclosed invoices.
     *
     * @return Collection
     */
    private function getVendors(): Collection
    {
        return $this->vendor->with(['vendorInvoice' => function ($query) {
            $query->where('implemented', 0);
        }])->get();
    }
}
