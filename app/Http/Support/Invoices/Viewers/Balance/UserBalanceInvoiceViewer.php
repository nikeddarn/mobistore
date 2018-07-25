<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 23.07.18
 * Time: 17:19
 */

namespace App\Http\Support\Invoices\Viewers\Balance;


use App\Http\Support\Invoices\Viewers\InvoiceViewer;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserBalanceInvoiceViewer extends InvoiceViewer
{
    /**
     * Get user balance invoices data for view.
     *
     * @param Collection|LengthAwarePaginator $invoices
     * @return array
     */
    public function getInvoicesData($invoices): array
    {
        $invoicesData = [];

        if ($invoices->count()) {

            $invoicesData['invoices'] = $this->createInvoicesData($invoices);

            if ($invoices instanceof LengthAwarePaginator) {
                $invoicesData['links'] = $invoices->links();
            }
        }

        return $invoicesData;
    }

    /**
     * Create invoices data.
     *
     * @param $invoices
     * @return array
     */
    private function createInvoicesData($invoices): array
    {
        $invoicesData = [];

        foreach ($invoices as $invoice) {

            $invoiceData = [
                'id' => $invoice->id,
                'createdAt' => $this->formatDate($invoice->created_at),
                'type' => $invoice->invoiceType->title,
                'sum' => $invoice->invoice_sum,
                'direction' => $invoice->userInvoice->direction,
            ];

            if ($invoice->invoiceProducts->count()) {
                $invoiceData['details'] = $this->getInvoiceProductsDetails($invoice);
            }

            if ($invoice->invoiceDefectProducts->count()) {
                $invoiceData['details'] = $this->getInvoiceDefectProductsDetails($invoice);
            }

            $invoicesData[] = $invoiceData;
        }

        return $invoicesData;
    }

    /**
     * Get invoice products details.
     *
     * @param Invoice $invoice
     * @return array
     */
    private function getInvoiceProductsDetails(Invoice $invoice): array
    {
        $invoiceProductsData = [];
        $allProductsSum = 0;

        foreach ($invoice->invoiceProducts as $invoiceProduct) {

            $invoiceProductSum = $invoiceProduct->quantity * $invoiceProduct->price;

            $allProductsSum += $invoiceProductSum;

            $invoiceProductsData[] = [
                'productId' => $invoiceProduct->product->id,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => $invoiceProduct->price,
                'sum' => $invoiceProductSum,
            ];
        }

        return [
            'products' => $invoiceProductsData,
            'productsSum' => $this->formatUsdPrice($allProductsSum),
            'deliverySum' => $this->formatUsdPrice($invoice->delivery_sum),
        ];
    }

    /**
     * Get invoice defect products details.
     *
     * @param Invoice $invoice
     * @return array
     */
    private function getInvoiceDefectProductsDetails(Invoice $invoice): array
    {
        $invoiceDefectProductsData = [];
        $allProductsSum = 0;

        foreach ($invoice->invoiceDefectProducts as $invoiceDefectProduct) {

            $allProductsSum += $invoiceDefectProduct->price;

            $invoiceDefectProductsData[] = [
                'productId' => $invoiceDefectProduct->reclamation->product->id,
                'reclamationId' => $invoiceDefectProduct->reclamation->id,
                'productLabel' => $invoiceDefectProduct->reclamation->product_label,
                'title' => $invoiceDefectProduct->reclamation->product->page_title,
                'price' => $invoiceDefectProduct->price,
            ];
        }

        return [
            'defectProducts' => $invoiceDefectProductsData,
            'productsSum' => $allProductsSum,
            'deliverySum' => $invoice->delivery_sum,
        ];
    }
}