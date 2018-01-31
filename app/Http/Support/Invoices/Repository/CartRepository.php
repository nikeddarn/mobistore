<?php
/**
 * User cart invoice repository.
 */

namespace App\Http\Support\Invoice\Repository;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

final class CartRepository extends InvoiceRepository
{
    /**
     * Get user cart invoice by user id.
     *
     * @param int $userId
     * @return Model|null
     */
    public function getByUserId(int $userId)
    {
        $query = static::getRetrieveQueryByUserId($userId);

        return $query->first();
    }

    /**
     * Get user cart invoice by cookie.
     *
     * @param string $userCartCookie
     * @return Model|null
     */
    public function getByUserCookie(string $userCartCookie)
    {
        $query = static::getRetrieveQueryByCookie($userCartCookie);

        return $query->first();
    }

    /**
     * Create user cart invoice model by user id.
     *
     * @param int $userId
     * @return Invoice
     */
    public function createByUserId(int $userId): Invoice
    {
            $invoice = parent::makeInvoice(self::CART, self::OUTGOING);
            $userCart = $invoice->userCart()->create([
                'invoices_id' => $invoice->id,
                'users_id' => $userId,
            ]);

            return $invoice->setRelation('userCart', $userCart);
    }

    /**
     * Create user cart invoice model by user cookie.
     *
     * @param string $userCookie
     * @return Invoice
     */
    public function createByUserCookie(string $userCookie): Invoice
    {
        $invoice = parent::makeInvoice(self::CART, self::OUTGOING);
        $userCart = $invoice->userCart()->create([
            'invoices_id' => $invoice->id,
            'cookie' => $userCookie,
        ]);

        return $invoice->setRelation('userCart', $userCart);
    }

    /**
     * Delete expired carts.
     *
     * @return void
     */
    public function deleteExpired()
    {

    }

    /**
     * Make retrieve invoice query with has user cart constraint.
     *
     * @param int $invoiceId
     * @return Builder
     */
    protected function getRetrieveQueryByInvoiceId(int $invoiceId):Builder
    {
        $query = parent::getRetrieveQueryByInvoiceId($invoiceId);
        $query = $this->setUserIdConstraint($query);

        return $query;
    }

    /**
     * Make retrieve invoice query with user cart constraint by id.
     *
     * @param int $userId
     * @return Builder
     */
    protected function getRetrieveQueryByUserId(int $userId):Builder
    {
        $query = parent::getRetrieveQueryWithLimit();
        $query = $this->setUserIdConstraint($query, $userId);

        return $query->with('invoiceProduct');
    }

    /**
     * Make retrieve invoice query with has user cart constraint by cookie.
     *
     * @param string $userCartCookie
     * @return Builder
     */
    protected function getRetrieveQueryByCookie(string $userCartCookie):Builder
    {
        $query = parent::getRetrieveQueryWithLimit();
        $query = $this->setCartCookieConstraint($query, $userCartCookie);

        return $query->with('invoiceProduct');
    }

    /**
     * Set has user cart constraint (by id optional).
     *
     * @param Builder $query
     * @param int|null $userId
     * @return Builder
     */
    private function setUserIdConstraint(Builder $query, int $userId = null):Builder
    {
        if ($userId){
            $query->whereHas('userCart', function ($query) use ($userId){
                    $query->where('users_id', $userId);
                });
        }else{
            $query->has('userCart');
        }
        return $query->with('userCart');
    }

    /**
     * Set has user cart constraint by cookie.
     *
     * @param Builder $query
     * @param string $userCartCookie
     * @return Builder
     */
    private function setCartCookieConstraint(Builder $query, string $userCartCookie):Builder
    {
            return $query
                ->whereHas('userCart', function ($query) use ($userCartCookie){
                    $query->where('cookie', $userCartCookie);
                })
                ->with('userCart');
    }
}