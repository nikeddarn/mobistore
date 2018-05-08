<?php
/**
 * Show vendor orders with products.
 */

namespace App\Http\Controllers\Vendor;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Repositories\Vendor\VendorProductInvoiceRepository;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceConstraints;
use App\Models\Vendor;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorOrderController extends Controller
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
     * @param VendorProductInvoiceRepository $invoiceRepository
     */
    public function __construct(Vendor $vendor, VendorProductInvoiceRepository $invoiceRepository)
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

        $retrieveConstraints = (new VendorInvoiceConstraints())
            ->setVendorId($this->retrievedVendor->id)
            ->setInvoiceStatus(InvoiceStatusInterface::PROCESSING)
            ->setInvoiceType([
                InvoiceTypes::ORDER,
                InvoiceTypes::PRE_ORDER,
                InvoiceTypes::RETURN_ORDER
            ])
            ->setInvoiceDirection(InvoiceDirections::OUTGOING)
            ->setImplementedStatus(0)
            ->setPaginate(config('shop.account_items_show'));

        return view('content.vendor.order.index')->with([
            'outgoingOrders' => $this->getOutgoingOrders($retrieveConstraints),
            'outgoingProducts' => $this->getOutgoingProducts($retrieveConstraints),
            'vendorId' => $this->retrievedVendor->id,
            'vendorTitle' => $this->retrievedVendor->title,
        ]);
    }

    /**
     * Get not collected vendor orders.
     *
     * @param VendorInvoiceConstraints $constraints
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getOutgoingOrders(VendorInvoiceConstraints $constraints)
    {
        return $this->invoiceRepository->getInvoices($constraints);
    }

    /**
     * Get all not collected order's products.
     *
     * @param VendorInvoiceConstraints $constraints
     * @return Collection
     */
    private function getOutgoingProducts(VendorInvoiceConstraints $constraints): Collection
    {
        return $this->invoiceRepository->getRetrieveInvoicesQuery($constraints)
            ->leftJoin('invoice_products', 'invoices.id', '=', 'invoice_products.invoices_id')
            ->join('products', 'products.id', '=', 'invoice_products.products_id')
            ->join('vendor_products', 'vendor_products.products_id', '=', 'products.id')
            ->select(['products.page_title_' . app()->getLocale() . ' as page_title', 'vendor_products.vendor_product_id as vendor_product_id', DB::raw('sum(invoice_products.quantity) as total_quantity')])
            ->groupBy('vendor_products.vendor_product_id')
            ->get();
    }
}
