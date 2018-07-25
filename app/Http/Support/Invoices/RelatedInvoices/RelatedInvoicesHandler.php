<?php
/**
 * Handle user invoice that consists some vendor invoices.
 */

namespace App\Http\Support\Invoices\RelatedInvoices;


use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RelatedInvoicesHandler
{
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * RelatedInvoicesHandler constructor.
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get related user invoice to given vendor invoice.
     *
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function getRelatedUserInvoice(Invoice $invoice)
    {
        $relatedUserInvoice = $invoice->vendorInvoice->userInvoice->first();

        if ($relatedUserInvoice){
            return $relatedUserInvoice->invoice()->with('userInvoice')->first();
        }

        return null;
    }

    /**
     * Get all vendor invoices that consists given user invoice.
     *
     * @param Invoice $invoice
     * @return Collection
     */
    public function getRelatedVendorInvoicesByUserInvoice(Invoice $invoice): Collection
    {
        return $this->invoice->whereHas('vendorInvoice.userInvoiceHasVendorInvoice', function ($query) use ($invoice) {
            $query->where('user_invoices_id', $invoice->userInvoice->id);
        })
            ->with('vendorInvoice')
            ->get();
    }

    /**
     * Get all vendor invoices that consists user invoice from given one of vendor invoices.
     *
     * @param Invoice $invoice
     * @return Collection
     */
    public function getRelatedVendorInvoicesByVendorInvoice(Invoice $invoice): Collection
    {
        $relatedUserInvoice = $invoice->vendorInvoice->userInvoice()->first();

        if ($relatedUserInvoice) {
            return $this->invoice->whereHas('vendorInvoice.userInvoiceHasVendorInvoice', function ($query) use ($relatedUserInvoice) {
                $query->where('user_invoices_id', $relatedUserInvoice->id);
            })
                ->with('vendorInvoice')
                ->get();
        } else {
            return collect();
        }
    }

    /**
     * Are all the vendor invoices, of which the user invoice is composed, collected?
     *
     * @param Collection $relatedVendorInvoices
     * @return bool
     */
    public function areRelatedVendorInvoicesImplemented(Collection $relatedVendorInvoices):bool
    {
        // all vendor invoices that related with user invoice are implemented
        return !$relatedVendorInvoices->where('vendorInvoice.implemented', 0)->count();
    }

    /**
     * Update user delivery date.
     *
     * @param Invoice $invoice
     * @param Carbon $deliveryDate
     * @return bool
     */
    public function updateDeliveryDate(Invoice $invoice, Carbon $deliveryDate = null):bool
    {
        $userDelivery = $invoice->userInvoice->userDelivery;

        $oldDeliveryDate = $userDelivery->planned_arrival ? $userDelivery->planned_arrival->toDateString() : null;
        $newDeliveryDate = $deliveryDate ? $deliveryDate->toDateString() : null;

        if ($oldDeliveryDate !== $newDeliveryDate) {
            $userDelivery->planned_arrival = $deliveryDate ? $deliveryDate->toDateTimeString() : null;
            return $userDelivery->save();
        }else{
            return false;
        }
    }

    /**
     * Update user delivery status.
     *
     * @param Invoice $invoice
     * @param int $deliveryStatus
     * @return bool
     */
    public function updateDeliveryStatus(Invoice $invoice, int $deliveryStatus):bool
    {
        $invoice->userInvoice->delivery_status_id = $deliveryStatus;
        return $invoice->userInvoice->save();
    }
}