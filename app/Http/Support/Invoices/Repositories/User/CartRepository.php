<?php
/**
 * User cart invoice repository.
 */

namespace App\Http\Support\Invoices\Repositories\User;

use App\Contracts\Shop\Invoices\Repositories\CartRepositoryInterface;
use App\Http\Support\Invoices\Repositories\InvoiceRepository;
use App\Models\Invoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class CartRepository extends InvoiceRepository implements CartRepositoryInterface
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

    /**
     * Is invoice with given user's id or cookie exist ?
     * Retrieve and check user cart.
     *
     * @return bool
     */
    public function cartExists(): bool
    {
        // retrieve by user's id
        if (auth('web')->check()) {
            static::buildRetrieveQueryByUserId(auth('web')->user()->id);
            $this->retrievedInvoice = $this->retrieveQuery->first();
        }

        // retrieve by cart cookie
        if (!$this->retrievedInvoice && request()->hasCookie(self::CART_COOKIE_NAME)) {
            static::buildRetrieveQueryByCookie(request()->cookie(self::CART_COOKIE_NAME));
            $this->retrievedInvoice = $this->retrieveQuery->first();

            // associate retrieved by cookie cart with logged in user.
            if ($this->retrievedInvoice && auth('web')->check()) {
                $this->retrievedInvoice->userCart->users_id = auth('web')->user()->id;
                $this->retrievedInvoice->userCart->save();
            }
        }

        // check cart
        if ($this->retrievedInvoice && !$this->checkUserCartExpired($this->retrievedInvoice)) {
            // destroy expired cart
            $this->retrievedInvoice->delete();
            $this->retrievedInvoice = null;
        }

        return (bool)$this->retrievedInvoice;
    }

    /**
     * Get user cart invoice by user id.
     *
     * @param int $userId
     * @return Model|null
     */
    public function getByUserId(int $userId)
    {
        static::buildRetrieveQueryByUserId($userId);

        $this->retrievedInvoice = $this->retrieveQuery->first();

        if (!$this->checkUserCartExpired($this->retrievedInvoice)) {
            $this->retrievedInvoice->delete();
            $this->retrievedInvoice = null;
        }

        return $this->retrievedInvoice;
    }

    /**
     * Get user cart invoice by cookie.
     *
     * @param string $userCartCookie
     * @return Model|null
     */
    public function getByUserCookie(string $userCartCookie)
    {
        static::buildRetrieveQueryByCookie($userCartCookie);

        $this->retrievedInvoice = $this->retrieveQuery->first();

        if (!$this->checkUserCartExpired($this->retrievedInvoice)) {
            $this->retrievedInvoice->delete();
            $this->retrievedInvoice = null;
        }

        return $this->retrievedInvoice;
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
     * @return void
     */
    protected function buildRetrieveQueryByInvoiceId(int $invoiceId)
    {
        parent::buildRetrieveQueryByInvoiceId($invoiceId);

        $this->setUserIdConstraint();
    }

    /**
     * Make retrieve invoice query with user cart constraint by id.
     *
     * @param int $userId
     * @return void
     */
    protected function buildRetrieveQueryByUserId(int $userId)
    {
        parent::makeRetrieveInvoiceQuery();

        $this->setUserIdConstraint($userId);

        $this->retrieveQuery->with('invoiceProduct.product.primaryImage');
    }

    /**
     * Make retrieve invoice query with has user cart constraint by cookie.
     *
     * @param string $userCartCookie
     * @return void
     */
    protected function buildRetrieveQueryByCookie(string $userCartCookie)
    {
        parent::makeRetrieveInvoiceQuery();

        $this->setCartCookieConstraint($userCartCookie);

        $this->retrieveQuery->with('invoiceProduct.product.primaryImage');
    }

    /**
     * Set has user cart constraint (by id, optional).
     *
     * @param int|null $userId
     * @return void
     */
    private function setUserIdConstraint(int $userId = null)
    {
        if ($userId) {
            $this->retrieveQuery->whereHas('userCart', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            });
        } else {
            $this->retrieveQuery->has('userCart');
        }

        $this->retrieveQuery->with('userCart');
    }

    /**
     * Set has user cart constraint by cookie.
     *
     * @param string $userCartCookie
     * @return void
     */
    private function setCartCookieConstraint(string $userCartCookie)
    {
        $this->retrieveQuery->whereHas('userCart', function ($query) use ($userCartCookie) {
            $query->where('cookie', $userCartCookie);
        })
            ->with('userCart');
    }

    /**
     * Check if cart is up to date.
     *
     * @param Invoice $userCartInvoice
     * @return bool
     */
    private function checkUserCartExpired(Invoice $userCartInvoice): bool
    {
        return $userCartInvoice && $userCartInvoice->updated_at > Carbon::now()->subDays(config('shop.user_cart_ttl'));
    }

    /**
     * Destroy cart invoice.
     *
     * @param Invoice|Model $invoice
     * @return bool
     * @throws Exception
     */
    protected function removeInvoiceData(Invoice $invoice)
    {
        $invoice->delete();

        return true;
    }
}