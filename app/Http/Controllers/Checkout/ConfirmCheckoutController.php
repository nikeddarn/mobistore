<?php

namespace App\Http\Controllers\Checkout;

use App\Contracts\Shop\Delivery\DeliveryTypesInterface;
use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Events\Invoices\UserOrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Support\Checkout\UserDeliveryRepository;
use App\Http\Support\Checkout\UserInvoiceProductsSorter;
use App\Http\Support\Checkout\UserInvoicesCreator;
use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\UserOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\UserOutgoingOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorIncomingOrderInvoiceFabric;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
use App\Http\Support\Price\DeliveryPrice;
use App\Http\Support\Price\ProductPrice;
use App\Http\Support\ProductRepository\StorageProductRepository;
use App\Http\Support\ProductRepository\StorageProductRouter;
use App\Http\Support\ProductRepository\VendorProductRepository;
use App\Http\Support\ProductRepository\VendorProductRouter;
use App\Http\Support\Shipment\LocalShipmentDispatcher;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\DeliveryType;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\PostService;
use App\Models\PreOrderInvoice;
use App\Models\UserInvoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ConfirmCheckoutController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var CartInvoiceFabric
     */
    private $cartInvoiceFabric;

    /**
     * @var UserInvoiceProductsSorter
     */
    private $productsSorter;

    /**
     * @var UserInvoicesCreator
     */
    private $invoicesCreator;

    /**
     * @var UserDeliveryRepository
     */
    private $deliveryRepository;
    /**
     * @var DeliveryPrice
     */
    private $deliveryPrice;

    /**
     * ConfirmCheckoutController constructor.
     * @param Request $request
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param UserInvoiceProductsSorter $productsSorter
     * @param UserInvoicesCreator $invoicesCreator
     * @param UserDeliveryRepository $deliveryRepository
     * @param DeliveryPrice $deliveryPrice
     */
    public function __construct(Request $request, CartInvoiceFabric $cartInvoiceFabric, UserInvoiceProductsSorter $productsSorter, UserInvoicesCreator $invoicesCreator, UserDeliveryRepository $deliveryRepository, DeliveryPrice $deliveryPrice)
    {
        $this->request = $request;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->productsSorter = $productsSorter;
        $this->invoicesCreator = $invoicesCreator;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * Create user order invoices.
     *
     * @return $this
     * @throws Exception
     */
    public function confirmOrder()
    {
        // user must be authenticated
        if (!auth('web')->check()) {
            abort(401);
        }

        // validate delivery form
        $this->validate($this->request, $this->getValidateRules());

        $cartRepository = $this->cartInvoiceFabric->getRepository();

        if (!$cartRepository->cartExists()) {
            return redirect('cart.show');
        }

        $cartHandler = $this->cartInvoiceFabric->getHandler()->bindInvoice($cartRepository->getRetrievedInvoice());

        // cart is empty
        if (!$cartHandler->getProductsCount()) {
            return redirect('cart.show');
        }

        // create user invoices
        $this->createUserInvoices($cartHandler);

        // destroy cart
        $cartRepository->deleteRetrievedInvoice();

        return redirect(route('message.show'));
    }

    /**
     * Validate request data rules.
     *
     * @return array
     */
    private function getValidateRules()
    {
        return [
            'name' => 'required|string|max:64',
            'phone' => 'required|string|max:32',
            'address' => 'required|string|max:256',
            'message' => 'nullable|string|max:256',
        ];
    }


    /**
     * Create invoices
     *
     * @param ProductInvoiceHandlerInterface $cartHandler
     * @throws Exception
     */
    private function createUserInvoices(ProductInvoiceHandlerInterface $cartHandler)
    {
        $sortedInvoices = $this->productsSorter->sortProductsByOrderType($cartHandler->getInvoiceProducts());

            // define delivery price
            $deliveryPrice = $this->deliveryPrice->calculateDeliveryPrice(auth('web')->user(), $cartHandler->getInvoiceSum(), (int)$this->request->get('delivery_type'));


            if (!empty($sortedInvoices[InvoiceTypes::ORDER])) {
                // sort products by storages
                $sortedStoragesInvoices = $this->productsSorter->sortProductByStorages($sortedInvoices[InvoiceTypes::ORDER]);
                // create storages invoices
                $storageUserInvoice = $this->invoicesCreator->createStorageInvoices($sortedInvoices[InvoiceTypes::ORDER], $sortedStoragesInvoices);
                // fire event
                event(new UserOrderCreated($storageUserInvoice));
            }

            if (!empty($sortedInvoices[InvoiceTypes::PRE_ORDER])) {
                // sort products by vendors
                $sortedVendorsInvoices = $this->productsSorter->sortProductByStorages($sortedInvoices[InvoiceTypes::PRE_ORDER]);
                // create vendor invoices
                $vendorUserInvoice = $this->invoicesCreator->createStorageInvoices($sortedInvoices[InvoiceTypes::PRE_ORDER], $sortedVendorsInvoices);
                // fire event
                event(new UserOrderCreated($vendorUserInvoice));
            }


//        $user = auth('web')->user()->id;
//
//        // define delivery data
//        $deliveryTypeId = (int)$this->request->get('delivery_type');
//        if ($deliveryTypeId === self::COURIER) {
//            $deliveryPrice = $this->deliveryPrice->calculateDeliveryPrice(auth('web')->user(), $this->cartHandler->getInvoiceSum());
//        } else {
//            $deliveryPrice = 0;
//        }
//
//        // make online order invoice
//        if ($orderProducts[self::ORDER]->count()) {
//            $deliveryDay = $this->localShipmentDispatcher->getPossibleNearestShipmentArrival();
//            $userOrderInvoice = $this->createUserInvoice(self::ORDER, $user, $orderProducts[self::ORDER], $deliveryTypeId, $deliveryPrice, $deliveryDay);
//
//            event(new UserOrderCreated($userOrderInvoice));
//        }
//
//        // make pre order invoice
//        if ($orderProducts[self::PRE_ORDER]->count()) {
//
//            // set free delivery for pre order if online order is present
//            if ($orderProducts[self::ORDER]->count()) {
//                $deliveryPrice = 0;
//            }
//
//            $deliveryDay = $this->vendorShipmentDispatcher->getPossibleNearestShipmentArrival();
//            $vendorOrderInvoice = $this->createVendorInvoice($orderProducts[self::PRE_ORDER]);
//            $userPreOrderInvoice = $userOrderInvoice = $this->createUserInvoice(self::PRE_ORDER, $user, $orderProducts[self::PRE_ORDER], $deliveryTypeId, $deliveryPrice, $deliveryDay, $vendorOrderInvoice->vendorInvoice->id);
//
//            event(new UserOrderCreated($userPreOrderInvoice));
//        }
    }

    /**
     * Create user order invoice.
     *
     * @param int $invoiceType
     * @param int $userId
     * @param Collection $orderProducts
     * @param int $deliveryTypeId
     * @param float $deliveryPrice
     * @param string $deliveryDay
     * @param int $relatedVendorInvoiceId
     * @return Invoice
     * @throws Exception
     */
    private function createUserInvoice(int $invoiceType, int $userId, Collection $orderProducts, int $deliveryTypeId, float $deliveryPrice, string $deliveryDay, int $relatedVendorInvoiceId = null): Invoice
    {
        // define outgoing storage
        $storageId = $this->storageProductRouter->defineInvoiceStorage($orderProducts);

        //create and bind invoice to handler
        $invoice = $this->userOutgoingOrderInvoiceFabric->getCreator()->createInvoice($invoiceType, $userId, $storageId, $deliveryTypeId);
        $handleableUserInvoice = $this->userOutgoingOrderInvoiceFabric->getHandler()->bindInvoice($invoice);
        $orderProducts->each(function (InvoiceProduct $invoiceProduct) use ($handleableUserInvoice) {
            $handleableUserInvoice->appendProducts($invoiceProduct->products_id, $invoiceProduct->price, $invoiceProduct->quantity);
        });

        // add delivery data
        $handleableUserInvoice->setInvoiceDeliverySum($deliveryPrice);
        $handleableUserInvoice->addUserDelivery($this->request->only(['name', 'phone', 'address', 'message']), $deliveryDay);

        // relate vendor invoice with user invoice
        if ($relatedVendorInvoiceId) {
            $handleableUserInvoice->bindVendorInvoice($relatedVendorInvoiceId);
        }

        return $invoice;
    }

    /**
     * Create user order invoice.
     *
     * @param Collection $orderProducts
     * @return Invoice
     * @throws Exception
     */
    private function createVendorInvoice(Collection $orderProducts): Invoice
    {
        // define outgoing storage
        $storageId = $this->storageProductRouter->defineInvoiceStorage($orderProducts);
        //define vendor
        $vendorId = $this->vendorProductRouter->defineInvoiceVendor($orderProducts);

        //create and bind invoice to handler
        $invoice = $this->vendorIncomingOrderInvoiceFabric->getCreator()->createInvoice(self::PRE_ORDER, $vendorId, $storageId);
        $handleableVendorInvoice = $this->vendorIncomingOrderInvoiceFabric->getHandler()->bindInvoice($invoice);

        // add products to invoice
        $orderProducts->each(function (InvoiceProduct $invoiceProduct) use ($handleableVendorInvoice, $vendorId) {
            $handleableVendorInvoice->appendProducts($invoiceProduct->products_id, $this->productPrice->getVendorPriceByProductId($invoiceProduct->products_id, $vendorId), $invoiceProduct->quantity);
        });

        return $invoice;
    }

    /**
     * Remove unavailable products from cart.
     *
     * @param ProductInvoiceHandlerInterface $cartHandler
     * @param Collection $unavailableProducts
     */
    private function removeUnavailableProducts(ProductInvoiceHandlerInterface $cartHandler, Collection $unavailableProducts)
    {
        foreach ($unavailableProducts as $product){
            $cartHandler->decreaseProductCount($product->id, $product->quantity);
        }
    }
}
