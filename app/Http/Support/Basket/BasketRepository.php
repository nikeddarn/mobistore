<?php
/**
 * User basket repository.
 */

namespace App\Http\Support\Basket;


use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class BasketRepository
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

    /**
     * Create basket invoice.
     *
     * @param User $user
     * @return Invoice
     */
    public function create(User $user): Invoice
    {

    }

    /**
     * Is basket exist ?
     *
     * @param User $user
     * @return bool
     */
    public function exists(User $user): bool
    {
        if (!isset($this->userBasketInvoice)) {
            $this->userBasketInvoice = $this->retrieveUserBasketInvoice($user);
        }

        return (bool)$this->userBasketInvoice;
    }


    /**
     * Delete user's basket.
     *
     * @param User $user
     * @return void
     */
    public function delete(User $user)
    {

    }

    /**
     * Delete expired baskets.
     *
     * @return void
     */
    public function deleteExpired()
    {

    }

    /**
     * Retrieve user basket by user or cookie.
     *
     * @param User $user
     * @return Invoice|null
     */
    private function retrieveUserBasketInvoice(User $user)
    {
        if ($user) {
            return $this->invoice->whereHas('userBasket', function ($query) use ($user) {
                $query->where('users_id', $user->id);
            })
                ->with('invoiceProduct.product')
                ->first();
        } else {
            $basketCookie = $this->request->cookie($this->basketCookieName);

            if ($basketCookie) {
                return $this->invoice->whereHas('userBasket', function ($query) use ($basketCookie) {
                    $query->where('cookie', $basketCookie);
                })
                    ->with('invoiceProduct.product')
                    ->first();
            } else {
                return null;
            }
        }
    }
}