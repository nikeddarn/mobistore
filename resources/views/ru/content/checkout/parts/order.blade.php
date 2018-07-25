<div class="table-responsive">

    <h4>
        <span class="text-gray">Онлайн заказ</span>&nbsp;&nbsp;
        <span class="small">Ориентировочная дата доставки: {{ $productsData['order']['delivery_time'] }}</span>
    </h4>
    <table class="table table-bordered cart-product-list">

        <thead>
        <tr>
            <th>Продукт</th>
            <th>Описание</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Итого</th>
            <th>Действие</th>
        </tr>
        </thead>

        <tbody>

        @foreach($productsData['order']['products'] as $product)

            <form id="product-{{$product['id']}}" method="post" action="{{ route('cart.set.count') }}">
                {{csrf_field()}}
                <input type="hidden" form="product-{{$product['id']}}" value="{{$product['id']}}" name="id">
            </form>
            <tr>

                <td class="cart-product-image">
                    <a href="/product/{{ $product['url'] }}">
                        <img alt="Изображение {{ $product['title'] }}" src="{{ $product['image'] }}"
                             class="img-thumbnail">
                    </a>
                </td>
                <td class="cart-product-title">
                    <p><a href="/product/{{ $product['url'] }}">{{ $product['title'] }}</a></p>
                </td>
                <td class="product-quantity-wrapper">
                    <input class="cart-product-quantity" type="text" value="{{ $product['quantity'] }}"
                           name="quantity" form="product-{{$product['id']}}">
                </td>
                <td class="cart-product-unit">${{ $product['price'] }}</td>
                <td class="cart-product-sub">${{ $product['total'] }}</td>
                <td class="cart-product-action">
                    <button type="submit" class="btn btn-link" form="product-{{$product['id']}}"
                            data-toggle="tooltip" data-placement="top" data-original-title="Обновить"><i
                                class="fa fa-refresh"></i></button>&nbsp;
                    <a href="/cart/remove/{{$product['id']}}" class="text-danger" data-toggle="tooltip"
                       data-placement="top" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>

        @endforeach

        <tr>
            <td colspan="4" class="text-right border-bottom-none border-left-none">Сумма заказа</td>
            <td colspan="2">
                <b class="pull-left">{{ $productsData['order']['invoice_sum'] }}</b>
                <b class="pull-right invoice-uah-sum">{{ $productsData['order']['invoice_uah_sum'] }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-right border-bottom-none border-top-none border-left-none">Доставка</td>
            <td colspan="2">
                <b class="pull-right invoice-delivery-uah-sum"
                   data-post-delivery-text="{{ $productsData['order']['post_delivery-message'] }}">{{ $productsData['order']['delivery_uah_sum'] }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-right border-top-none border-bottom-none border-left-none">Итого</td>
            <td colspan="2">
                <b class="pull-right invoice-uah-total-sum">{{ $productsData['order']['total_uah_sum'] }}</b>
            </td>
        </tr>

        </tbody>
    </table>

</div>
