<table class="table table-responsive">

    <thead>
    <tr class="text-center">
        <td>Код</td>
        <td>Наименование продукта</td>
        <td>Количество</td>
        <td>Цена</td>
        <td>Сумма</td>
    </tr>
    </thead>

    <tbody>
    @foreach($invoice['details']['products'] as $product)
        <tr class="text-center">
            <td>{{ $product['productId'] }}</td>
            <td>{{ $product['title'] }}</td>
            <td>{{ $product['quantity'] }}</td>
            <td>{{ $product['price'] }}</td>
            <td>{{ $product['sum'] }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4" class="text-right">Итого:</td>
        <td>{{ $invoice['details']['productsSum'] }}</td>
    </tr>

    @if($invoice['details']['deliverySum'] > 0)
        <tr>
            <td colspan="4" class="text-right">Доставка:</td>
            <td>{{ $invoice['details']['deliverySum'] }}</td>
        </tr>
    @endif

    </tbody>
</table>