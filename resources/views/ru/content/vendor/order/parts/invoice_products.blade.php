<table class="table table-responsive invoice-products-table">
    <thead>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Артикул</td>
        <td class="text-center">Количество</td>
        <td class="text-center">Заказано</td>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->invoiceProduct as $invoiceProduct)
        <tr>
            <td>{{ $invoiceProduct->product->page_title }}</td>
            <td class="text-center">{{ $invoiceProduct->product->vendorProduct->first()->vendor_product_id }}</td>
            <td class="text-center product-needing-quantity">{{ $invoiceProduct->quantity }}</td>
            <td class="text-center product-ordered-quantity">
                <input class="cart-product-quantity" form="collect-{{ $invoice->id }}" type="text" value="{{ $invoiceProduct->quantity }}"
                       name="quantity[{{ $invoiceProduct->id }}]">
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4" class="text-center">
            <button class="btn btn-primary pull-right" form="collect-{{ $invoice->id }}" type="submit">Отметить как собранный</button>
        </td>
    </tr>
    </tbody>
</table>