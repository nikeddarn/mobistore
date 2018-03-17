<?php
/**
 * Cart repository interface.
 */

namespace App\Contracts\Shop\Invoices\Repositories;


use Illuminate\Database\Eloquent\Model;

interface CartRepositoryInterface extends InvoiceRepositoryInterface
{
    /**
     * Is invoice with given user's id or cookie exist ?
     *
     * @return bool
     */
    public function cartExists(): bool;

    /**
     * Get user cart invoice by user id.
     *
     * @param int $userId
     * @return Model|null
     */
    public function getByUserId(int $userId);

    /**
     * Get user cart invoice by cookie.
     *
     * @param string $userCartCookie
     * @return Model|null
     */
    public function getByUserCookie(string $userCartCookie);

    /**
     * Delete expired carts.
     *
     * @return void
     */
    public function deleteExpired();
}