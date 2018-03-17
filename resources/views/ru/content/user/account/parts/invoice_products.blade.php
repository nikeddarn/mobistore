<table class="table table-responsive">
    <thead>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Количество</td>
        <td class="text-center">Цена</td>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->invoiceProduct as $invoiceProduct)
        <tr>
            <td>{{ $invoiceProduct->product->page_title }}</td>
            <td class="text-center">{{ $invoiceProduct->quantity }}</td>
            <td class="text-center">${{ $invoiceProduct->price }}</td>
        </tr>
    @endforeach
    </tbody>
</table>