<?php
/**
 * Create user cart by user's id or cookie.
 */

namespace App\Http\Support\Invoices\Creators\CartInvoiceCreators\FinalInvoices;

use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Creators\InvoiceCreator;
use App\Models\Invoice;
use App\Models\UserCart;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class CartInvoiceCreator extends InvoiceCreator implements InvoiceCreatorInterface
{
    /**
     * @var int
     */
    private $userId = null;

    /**
     * @var string
     */
    private $userCookie = null;

    /**
     * Create user cart invoice model by user's id or user's cookie.
     *
     * @param int $userId
     * @param string|null $userCookie
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $userId = null, string $userCookie = null): Invoice
    {
        assert($userId || $userCookie, 'There is not userId or userCookie');

        try {
            $this->databaseManager->beginTransaction();

            $this->userId = $userId;
            $this->userCookie = $userCookie;

            $invoice =  $this->makeInvoice();

            $this->databaseManager->commit();

            return $invoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Make invoice.
     *
     * @return Invoice
     */
    protected function makeInvoice():Invoice
    {
        $invoice = parent::makeInvoice();

        return $invoice->setRelation('userCart', $this->makeUserCart($invoice));
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return array_merge(parent::getInvoiceData(), [
            'invoice_types_id' => InvoiceTypes::USER_CART,
        ]);
    }

    /**
     * Make UserCart.
     *
     * @param Invoice $invoice
     * @return UserCart|Model
     */
    private function makeUserCart(Invoice $invoice):UserCart
    {
        return $invoice->userCart()->create($this->getUserCartInvoiceData());
    }

    /**
     * Get array of data for create VendorInvoice model.
     *
     * @return array
     */
    private function getUserCartInvoiceData():array
    {
        return [
            'users_id' => $this->userId,
            'cookie' => $this->userCookie,
        ];
    }
}