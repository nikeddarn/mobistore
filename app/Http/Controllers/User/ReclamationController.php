<?php

namespace App\Http\Controllers\User;

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReclamationController extends Controller
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

        return view('content.user.warranty.index')->with([
            'userWarranties' => $this->getWarrantyData(),
            'userReclamationStock' => $this->getUserReclamationStock(),
            'commonMetaData' => [
                'title' => trans('meta.title.user.warranty'),
            ],
        ]);
    }

    /**
     * Get user account items.
     *
     * @return LengthAwarePaginator
     */
    private function getWarrantyData(): LengthAwarePaginator
    {
        return $this->invoice
            ->whereHas('userInvoice', function ($query) {
                $query->where('users_id', $this->user->id);
            })
            ->whereIn('invoice_types_id', [
                InvoiceTypes::RECLAMATION,
                InvoiceTypes::WRITE_OFF_RECLAMATION,
                InvoiceTypes::EXCHANGE_RECLAMATION,
                InvoiceTypes::RETURN_RECLAMATION,
            ])
            ->with('invoiceType', 'invoiceStatus', 'invoiceReclamation.product', 'invoiceReclamation.rejectReclamationReason')
            ->paginate(config('shop.warranty_items_show'));
    }

    /**
     * Get user reclamation stock
     *
     * @return Collection
     */
    private function getUserReclamationStock():Collection
    {
        return $this->user
            ->userReclamation()
            ->orderByDesc('updated_at')
            ->with('product')
            ->get();
    }
}
