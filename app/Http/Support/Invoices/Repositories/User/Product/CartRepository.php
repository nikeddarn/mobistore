<?php
/**
 * User cart invoice repository.
 */

namespace App\Http\Support\Invoices\Repositories\User;

use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use Illuminate\Database\Eloquent\Model;

final class CartRepository extends InvoiceRepository
{

    /**
     * Get cart by user's id or cart's cookie.
     *
     * @param null $user
     * @param string|null $cookie
     * @return Model|null
     */
    public function getCart($user = null, string $cookie = null)
    {
        $cart = null;

        if ($user){
            $cart = $this->getByUserId($user->id);
        }

        if (!$cart && $cookie){
            $cart = $this->getByUserCookie($cookie);
        }

        return $cart;
    }

    /**
     * Get user cart invoice by user id.
     *
     * @param int $userId
     * @return Model|null
     */
    public function getByUserId(int $userId)
    {
        return parent::makeQuery()
            ->whereHas('userCart', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->with('invoiceProducts.product.primaryImage', 'userCart.user')
            ->first();
    }

    /**
     * Get user cart invoice by cookie.
     *
     * @param string $userCartCookie
     * @return Model|null
     */
    public function getByUserCookie(string $userCartCookie)
    {
        return parent::makeQuery()
            ->whereHas('userCart', function ($query) use ($userCartCookie) {
                $query->where('cookie', $userCartCookie);
            })
            ->with('invoiceProducts.product.primaryImage', 'userCart.user')
            ->first();
    }

    /**
     * Delete expired carts.
     *
     * @return void
     */
    public function deleteExpired()
    {

    }
}