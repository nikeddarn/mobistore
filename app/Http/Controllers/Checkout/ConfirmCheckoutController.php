<?php

namespace App\Http\Controllers\Checkout;

use App\Contracts\Shop\Delivery\DeliveryTypesInterface;
use App\Events\Invoices\UserOrderCreated;
use App\Events\Invoices\UserPreOrderCreated;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Support\Collection;

class ConfirmCheckoutController extends CheckoutController implements DeliveryTypesInterface
{
    /**
     * @return $this
     * @throws Exception
     */
    public function confirmOrder()
    {
        if (!auth('web')->check()) {
            abort(401);
        }

        // keep cart referrer link
        $this->request->session()->keep(self::CART_REFERRER_SESSION_NAME);

        $this->validate($this->request, $this->getValidateRules());


        $this->cartHandler = $this->getCartHandler();

        // products in cart doesn't exist. show view with 'no products' message
        if (!$this->cartHandler) {
            return view('content.checkout.index')->with($this->commonMetaData());
        }

        // sort cart products by invoice types.
        $orderProducts = $this->sortProductsByInvoices();

        // create invoices
        $this->createUserInvoices($orderProducts);

        // delete cart invoice
        $this->cartInvoiceFabric->getRepository()->deleteRetrievedInvoice();

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
     * @param array $orderProducts
     * @throws Exception
     */
    private function createUserInvoices(array $orderProducts)
    {
        $user = auth('web')->user()->id;

        // define delivery data
        $deliveryTypeId = (int)$this->request->get('delivery_type');
        if ($deliveryTypeId === self::COURIER) {
            $deliveryPrice = $this->deliveryPrice->getDeliveryPrice(auth('web')->user(), $this->cartHandler->getInvoiceSum());
        }else{
            $deliveryPrice = 0;
        }

        // make online order invoice
        if ($orderProducts[self::ORDER]->count()) {
            $deliveryDay = $this->localShipmentDispatcher->getPossibleNearestShipmentArrival();
            $userOrderInvoice = $this->createUserInvoice(self::ORDER, $user, $orderProducts[self::ORDER], $deliveryTypeId, $deliveryPrice, $deliveryDay);

            event(new UserOrderCreated($userOrderInvoice));
        }

        // make pre order invoice
        if ($orderProducts[self::PRE_ORDER]->count()) {

            // set free delivery for pre order if online order is present
            if ($orderProducts[self::ORDER]->count()) {
                $deliveryPrice = 0;
            }

            $deliveryDay = $this->vendorShipmentDispatcher->getPossibleNearestShipmentArrival();
            $vendorOrderInvoice = $this->createVendorInvoice($orderProducts[self::PRE_ORDER]);
            $userPreOrderInvoice = $userOrderInvoice = $this->createUserInvoice(self::PRE_ORDER, $user, $orderProducts[self::PRE_ORDER], $deliveryTypeId, $deliveryPrice, $deliveryDay, $vendorOrderInvoice->vendorInvoice->id);

            event(new UserPreOrderCreated($userPreOrderInvoice));
        }
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
        if ($relatedVendorInvoiceId){
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
        $invoice = $this->vendorIncomingOrderInvoiceFabric->getCreator()->createInvoice(self::ORDER, $vendorId, $storageId);
        $handleableVendorInvoice = $this->vendorIncomingOrderInvoiceFabric->getHandler()->bindInvoice($invoice);

        // add products to invoice
        $orderProducts->each(function (InvoiceProduct $invoiceProduct) use ($handleableVendorInvoice, $vendorId) {
            $handleableVendorInvoice->appendProducts($invoiceProduct->products_id, $this->productPrice->getVendorPriceByProductId($invoiceProduct->products_id, $vendorId), $invoiceProduct->quantity);
        });

        return $invoice;
    }
}
