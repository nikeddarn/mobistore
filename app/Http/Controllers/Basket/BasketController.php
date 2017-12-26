<?php

/**
 * User Cart controller.
 */

namespace App\Http\Controllers\Basket;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BasketController extends Controller
{
    /**
     * @var string
     */
    private $basketCookieName = 'basket';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var Invoice
     */
    private $userBasketInvoice;

    /**
     * BasketController constructor.
     * @param Request $request
     * @param Invoice $invoice
     */
    public function __construct(Request $request, Invoice $invoice)
    {

        $this->request = $request;
        $this->invoice = $invoice;
    }

    public function basketExists()
    {
        if (!isset($this->userBasketInvoice)){
            $this->userBasketInvoice = $this->retrieveUserBasketInvoice();
        }
        var_dump($this->userBasketInvoice);
    }

    public function getItemsCount()
    {
        if (!isset($this->userBasketInvoice)){
            $this->userBasketInvoice = $this->retrieveUserBasketInvoice();
        }
    }

    /**
     * Retrieve user basket by user or cookie.
     *
     * @return Invoice|null
     */
    private function retrieveUserBasketInvoice()
    {
        $user = auth('web')->user();

        if ($user){
            return $this->invoice->whereHas('userBasket', function ($query) use ($user){
                $query->where('users_id', $user->id);
            })->first();
        }else{
            $basketCookie = $this->request->cookie($this->basketCookieName);

            if ($basketCookie){
                return $this->invoice->whereHas('userBasket', function ($query) use ($basketCookie){
                    $query->where('cookie', $basketCookie);
                })->first();
            }else{
                return null;
            }
        }
    }
}
