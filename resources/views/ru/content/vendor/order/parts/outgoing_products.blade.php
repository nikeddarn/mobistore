<table class="table table-responsive">
    <thead>
    <tr>
        <td colspan="3" class="text-center"><strong>Продукты в несобранных заказах</strong></td>
    </tr>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Артикул</td>
        <td class="text-center">Количество</td>
    </tr>
    </thead>
    <tbody>

    @foreach($outgoingProducts as $product)
        <tr>
            <td>{{ $product->page_title }}</td>
            <td class="text-center">{{ $product->vendor_product_id }}</td>
            <td class="text-center">{{ $product->total_quantity }}</td>
        </tr>
    @endforeach

    </tbody>
</table>