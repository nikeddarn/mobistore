<table class="table table-responsive">

    <thead>
    <tr class="text-center">
        <td>Код продукта</td>
        <td>Код рекламации</td>
        <td>Ярлык пользователя</td>
        <td>Наименование продукта</td>
        @if($invoice['details']['productsSum'] > 0)
            <td>Цена</td>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach($invoice['details']['defectProducts'] as $product)
        <tr class="text-center">
            <td>{{ $product['productId'] }}</td>
            <td>{{ $product['reclamationId'] }}</td>
            <td>{{ $product['productLabel'] }}</td>
            <td>{{ $product['title'] }}</td>
            @if($invoice['details']['productsSum'] > 0)
                <td>{{ $product['price'] }}</td>
            @endif
        </tr>
    @endforeach

    @if($invoice['details']['productsSum'] > 0)
    <tr>
        <td colspan="4" class="text-right">Итого:</td>
        <td>{{ $invoice['details']['productsSum'] }}</td>
    </tr>
    @endif

    @if($invoice['details']['deliverySum'] > 0)
    <tr>
        <td colspan="4" class="text-right">Доставка:</td>
        <td>{{ $invoice['details']['deliverySum'] }}</td>
    </tr>
    @endif

    </tbody>
</table>