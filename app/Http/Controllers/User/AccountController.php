<?php

namespace App\Http\Controllers\User;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * @var User
     */
    private $user;

    /**
     * AccountController constructor.
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Show user account items.
     *
     * @return View
     */
    public function show()
    {
        $this->user = auth('web')->user();

        return view('content.user.account.index')->with([
            'userInvoices' => $this->getAccountData(),
            'userBalance' => number_format($this->user->balance, 2, '.', ','),
            'commonMetaData' => [
                'title' => trans('meta.title.user.accounts'),
            ],
        ]);
    }

    /**
     * Get user account items.
     *
     * @return LengthAwarePaginator
     */
    private function getAccountData(): LengthAwarePaginator
    {
        return $this->invoice
            ->whereHas('userInvoice', function ($query) {
                $query->where('users_id', $this->user->id);
            })
            ->with('invoiceType', 'invoiceStatus', 'invoiceProduct.product', 'invoiceReclamation.product')
            ->paginate(config('shop.account_items_show'));
    }
}
