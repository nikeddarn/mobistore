<?php

namespace App\Http\Controllers\User;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\UserInvoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ShipmentController extends Controller implements InvoiceDirections
{
    /**
     * @var UserInvoice
     */
    private $userInvoice;

    /**
     * DeliveryController constructor.
     * @param UserInvoice $userInvoice
     */
    public function __construct(UserInvoice $userInvoice)
    {
        $this->userInvoice = $userInvoice;
    }

    public function show()
    {
        return view('content.user.deliveries.index')->with([
            'userDeliveries' => $this->getUserDeliveries(),
            'commonMetaData' => [
                'title' => trans('meta.title.user.deliveries'),
            ],
        ]);
    }

    /**
     * Get user's not delivered orders.
     *
     * @return Collection
     */
    private function getUserDeliveries(): Collection
    {
        $user = auth('web')->user();

        return $this->userInvoice
            ->where([
                ['users_id', $user->id],
                ['direction', self::INCOMING]
            ])
            ->whereHas('invoice', function ($query){
                $query->whereIn('invoice_types_id', [
                    InvoiceTypes::USER_ORDER,
                    InvoiceTypes::USER_PRE_ORDER,
                    InvoiceTypes::EXCHANGE_RECLAMATION,
                    InvoiceTypes::RETURN_RECLAMATION,
                ]);
            })
            ->with('deliveryStatus', 'deliveryType', 'userDelivery', 'invoice.invoiceProduct.product')
            ->get();
    }
}
