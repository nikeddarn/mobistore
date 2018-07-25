<table class="table table-responsive">
    <thead>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Артикул</td>
        <td class="text-center">Количество</td>
        <td class="text-center">Принято</td>
    </tr>
    </thead>
    <tbody>
        @foreach($invoice->invoiceProduct as $invoiceProduct)
            <tr>
                <td>{{ $invoiceProduct->product->page_title }}</td>
                <td class="text-center">{{ $invoiceProduct->product->id }}</td>
                <td class="text-center product-needing-quantity">{{ $invoiceProduct->quantity }}</td>
                <td class="text-center product-ordered-quantity">
                    <input class="cart-product-quantity" form="receive-{{ $invoice->id }}" type="text" value="{{ $invoiceProduct->quantity }}"
                           name="quantity[{{ $invoiceProduct->products_id }}]">
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" class="text-center">
                <button class="btn btn-primary pull-right" form="receive-{{ $invoice->id }}" type="submit">Отметить как принятый</button>
            </td>
        </tr>
    </tbody>
</table>