<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Support\Invoices\Repositories\User\VendorAccountInvoiceRepository;
use App\Http\Support\Invoices\Repositories\User\VendorProductInvoiceRepository;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceConstraints;
use App\Models\Vendor;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class VendorAccountController extends Controller
{
    /**
     * @var Vendor
     */
    private $vendor;

    /**
     * @var Vendor
     */
    private $retrievedVendor;

    /**
     * @var VendorProductInvoiceRepository
     */
    private $invoiceRepository;

    /**
     * AccountController constructor.
     * @param Vendor $vendor
     * @param VendorAccountInvoiceRepository $invoiceRepository
     */
    public function __construct(Vendor $vendor, VendorAccountInvoiceRepository $invoiceRepository)
    {
        $this->vendor = $vendor;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Show user account items.
     *
     * @param int|null $vendorId
     * @return View
     * @throws Exception
     */
    public function index(int $vendorId)
    {
        $this->retrievedVendor = $this->vendor->where('id', $vendorId)->first();

        if (!$this->retrievedVendor) {
            throw new Exception('Vendor is not defined.');
        }

        return view('content.vendor.account.index')->with([
            'vendorInvoices' => $this->getAccountData(),
            'vendorBalance' => number_format($this->retrievedVendor->balance, 2, '.', ','),
            'vendorId' => $this->retrievedVendor->id,
        ]);
    }

    /**
     * Get user account items.
     *
     * @return LengthAwarePaginator
     */
    private function getAccountData(): LengthAwarePaginator
    {
        return $this->invoiceRepository->getInvoices(
            (new VendorInvoiceConstraints())
                ->setVendorId($this->retrievedVendor->id)
                ->setPaginate(config('shop.account_items_show'))
        );
    }
}
