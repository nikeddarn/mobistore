<?php

namespace App\Http\Controllers\Checkout;

use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Events\Invoices\UserOrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Support\Checkout\UserDeliveryRepository;
use App\Http\Support\Checkout\UserInvoiceProductsSorter;
use App\Http\Support\Checkout\UserInvoicesCreator;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Price\DeliveryPrice;
use App\Http\Support\Shipment\LocalShipmentDispatcher;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use Exception;
use Illuminate\Http\Request;

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
     * @var LocalShipmentDispatcher
     */
    private $localShipmentDispatcher;

    /**
     * @var VendorShipmentDispatcher
     */
    private $vendorShipmentDispatcher;

    /**
     * ConfirmCheckoutController constructor.
     * @param Request $request
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param UserInvoiceProductsSorter $productsSorter
     * @param UserInvoicesCreator $invoicesCreator
     * @param UserDeliveryRepository $deliveryRepository
     * @param DeliveryPrice $deliveryPrice
     * @param LocalShipmentDispatcher $localShipmentDispatcher
     * @param VendorShipmentDispatcher $vendorShipmentDispatcher
     */
    public function __construct(Request $request, CartInvoiceFabric $cartInvoiceFabric, UserInvoiceProductsSorter $productsSorter, UserInvoicesCreator $invoicesCreator, UserDeliveryRepository $deliveryRepository, DeliveryPrice $deliveryPrice, LocalShipmentDispatcher $localShipmentDispatcher, VendorShipmentDispatcher $vendorShipmentDispatcher)
    {
        $this->request = $request;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->productsSorter = $productsSorter;
        $this->invoicesCreator = $invoicesCreator;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryPrice = $deliveryPrice;
        $this->localShipmentDispatcher = $localShipmentDispatcher;
        $this->vendorShipmentDispatcher = $vendorShipmentDispatcher;
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
        $cartHandler->deleteInvoice();

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
        // sort invoices by types
        $sortedInvoices = $this->productsSorter->sortProductsByOrderType($cartHandler->getInvoiceProducts());

        if (isset($sortedInvoices[InvoiceTypes::USER_ORDER]) || isset($sortedInvoices[InvoiceTypes::USER_PRE_ORDER])) {
            // order delivery city id
            $orderDeliveryCity = (int)$this->request->get('courier_delivery_city');
            // delivery type id
            $deliveryTypeId = $this->request->get('delivery_type');
            // user delivery model data
            $deliveryData = $this->request->only(['name', 'phone', 'address', 'message']);
            // define delivery price
            $deliveryPrice = $this->deliveryPrice->calculateDeliveryPrice(auth('web')->user(), $cartHandler->getInvoiceSum(), (int)$this->request->get('delivery_type'));

            // create order invoices
            if (!empty($sortedInvoices[InvoiceTypes::USER_ORDER])) {

                // sort products by storages
                $sortedStoragesInvoices = $this->productsSorter->sortProductByStorages($sortedInvoices[InvoiceTypes::USER_ORDER], $orderDeliveryCity);

                // create storages invoices
                $storageUserInvoiceHandler = $this->invoicesCreator->createUserOrderInvoices($sortedInvoices[InvoiceTypes::USER_ORDER], $sortedStoragesInvoices, $orderDeliveryCity);

                // add delivery sum
                $storageUserInvoiceHandler->setInvoiceDeliverySum($deliveryPrice);

                // define planned arrival
                $deliveryData['planned_arrival'] = $this->localShipmentDispatcher->calculateDeliveryDay(array_keys($sortedStoragesInvoices));

                // append delivery data
                $storageUserDelivery = $this->deliveryRepository->createUserDelivery($deliveryData);
                $storageUserInvoiceHandler->appendUserDelivery($storageUserDelivery->id, $deliveryTypeId);

                // fire event
                event(new UserOrderCreated($storageUserInvoiceHandler->getInvoice()));
            }

            // create pre order invoices
            if (!empty($sortedInvoices[InvoiceTypes::USER_PRE_ORDER])) {

                // sort products by vendors
                $sortedVendorsInvoices = $this->productsSorter->sortProductByVendors($sortedInvoices[InvoiceTypes::USER_PRE_ORDER]);

                // create vendor invoices
                $vendorUserInvoiceHandler = $this->invoicesCreator->createUserPreOrderInvoices($sortedInvoices[InvoiceTypes::USER_PRE_ORDER], $sortedVendorsInvoices, $orderDeliveryCity);

                // add delivery sum
                $vendorUserInvoiceHandler->setInvoiceDeliverySum($deliveryPrice);

                // define planned arrival
                $deliveryData['planned_arrival'] = $this->vendorShipmentDispatcher->calculateDeliveryDayByVendorShipments(array_keys($sortedVendorsInvoices));

                // append delivery data
                $vendorUserDelivery = $this->deliveryRepository->createUserDelivery($deliveryData);
                $vendorUserInvoiceHandler->appendUserDelivery($vendorUserDelivery->id, $deliveryTypeId);

                // fire event
                event(new UserOrderCreated($vendorUserInvoiceHandler->getInvoice()));
            }
        }
    }
}
