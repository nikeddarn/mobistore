<?php

namespace App\Http\Controllers\Storage;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Repositories\Storage\StorageInvoiceConstraints;
use App\Http\Support\Invoices\Repositories\Storage\StorageProductInvoiceRepository;
use App\Models\Shipment;
use App\Models\Storage;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StorageIncomingController extends Controller
{
    /**
     * @var Storage
     */
    private $retrievedStorage;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var StorageProductInvoiceRepository
     */
    private $storageProductInvoiceRepository;

    /**
     * StorageIncomingController constructor.
     * @param Storage $storage
     * @param Shipment $shipment
     * @param StorageProductInvoiceRepository $storageProductInvoiceRepository
     */
    public function __construct(Storage $storage, Shipment $shipment, StorageProductInvoiceRepository $storageProductInvoiceRepository)
    {
        $this->storage = $storage;
        $this->shipment = $shipment;
        $this->storageProductInvoiceRepository = $storageProductInvoiceRepository;
    }

    /**
     * Show storage products.
     *
     * @param int $storageId
     * @return View
     * @throws Exception
     */
    public function index(int $storageId)
    {
        $this->retrievedStorage = $this->storage->where('id', $storageId)->first();

        if (!$this->retrievedStorage) {
            throw new Exception('Storage is not defined.');
        }

        return view('content.storage.incoming.index')->with([
            'incomingShipments' => $this->getIncomingShipments(),
            'incomingShipmentsProducts' => $this->getIncomingShipmentsProducts(),
            'incomingInvoices' => $this->getIncomingInvoices(),
            'storageId' => $this->retrievedStorage->id,
            'storageTitle' => $this->retrievedStorage->title,
        ]);
    }

    /**
     * Get incoming shipments with invoices.
     *
     * @return Collection
     */
    private function getIncomingShipments(): Collection
    {
        return $this->shipment->whereHas('invoice', function ($query) {
            $query->where('invoice_status_id', InvoiceStatusInterface::PROCESSING);
            $query->whereHas('storageInvoice', function ($query) {
                $query->where('storages_id', $this->retrievedStorage->id);
                $query->where('direction', InvoiceDirections::INCOMING);
                $query->where('implemented', 0);
            });
        })
            ->whereNotNull('dispatched')
            ->whereNull('received')
            ->orderBy('dispatched')
            ->with(['invoice' => function ($query) {
                $query->where('invoice_status_id', InvoiceStatusInterface::PROCESSING);
                $query->whereHas('storageInvoice', function ($query) {
                    $query->where('direction', InvoiceDirections::INCOMING);
                    $query->where('implemented', 0);
                })
                    ->orderBy('invoice_types_id')
                    ->with('storageInvoice', 'invoiceType', 'invoiceProduct');
            }])
            ->get();
    }

    /**
     * Get incoming shipments with invoices.
     *
     * @return Collection
     */
    private function getIncomingShipmentsProducts(): Collection
    {
        $subQuery = $this->shipment->whereHas('invoice', function ($query) {
            $query->where('invoice_status_id', InvoiceStatusInterface::PROCESSING);
            $query->whereHas('storageInvoice', function ($query) {
                $query->where('storages_id', 1);
                $query->where('direction', InvoiceDirections::INCOMING);
                $query->where('implemented', 0);
            });
        })
            ->whereNotNull('dispatched')
            ->whereNull('received')
            ->join('invoices', 'invoices.shipments_id', '=', 'shipments.id')
            ->join('invoice_products', 'invoices.id', '=', 'invoice_products.invoices_id')
            ->join('products', 'products.id', '=', 'invoice_products.products_id')
            ->selectRaw('SUM(invoice_products.quantity) as quantity, products.id as product_id, products.page_title_' . app()->getLocale() . ' as product_title,  shipments.id as shipment_id')
            ->groupBy('products.id')
            ->groupBy('shipments.id');

        return $this->shipment->from(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery->getQuery())
            ->selectRaw('sub.shipment_id as shipment_id, CONCAT("[", GROUP_CONCAT(json_object("id", sub.product_id, "title", sub.product_title, "quantity", sub.quantity)), "]") as products')
            ->groupBy('sub.shipment_id')->get()
            ->keyBy('shipment_id');
    }

    /**
     * Get incoming invoices without shipments.
     *
     * @return Collection
     */
    private function getIncomingInvoices(): Collection
    {
        return $this->storageProductInvoiceRepository->getRetrieveInvoicesQuery(
            (new StorageInvoiceConstraints())
                ->setStorageId($this->retrievedStorage->id)
                ->setInvoiceStatus(InvoiceStatusInterface::PROCESSING)
                ->setInvoiceType([
                    InvoiceTypes::USER_ORDER,
                    InvoiceTypes::USER_PRE_ORDER,
                    InvoiceTypes::USER_RETURN_ORDER,
                    InvoiceTypes::RECLAMATION,
                    InvoiceTypes::EXCHANGE_RECLAMATION,
                    InvoiceTypes::RETURN_RECLAMATION,
                ])
                ->setInvoiceDirection(InvoiceDirections::INCOMING)
                ->setImplementedStatus(0)
        )
            ->doesntHave('shipment')
            ->where(function ($query) {
                $query->whereHas('userInvoice', function ($query) {
                    $query->where('implemented', 1);
                })
                    ->orWhereHas('vendorInvoice', function ($query) {
                        $query->where('implemented', 1);
                    })
                    ->orWhereHas('storageInvoice', function ($query) {
                        $query->where('implemented', 1);
                        $query->where('direction', InvoiceDirections::OUTGOING);
                    });
            })
            ->get();
    }
}
