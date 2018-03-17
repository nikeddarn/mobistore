<div class="table-responsive">

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

            @foreach($productsData['products'] as $product)

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
                                data-toggle="tooltip" data-placement="top" data-original-title="Update"><i
                                    class="fa fa-refresh"></i></button>&nbsp;
                        <a href="/cart/remove/{{$product['id']}}" class="text-danger" data-toggle="tooltip"
                           data-placement="top" data-original-title="Remove"><i class="fa fa-trash-o"></i></a>
                    </td>
                </tr>

            @endforeach

            <tr>
                <td colspan="4" class="text-right">Сумма заказа</td>
                <td colspan="2"><b class="pull-left">${{ $productsData['invoice_sum'] }}</b><b
                            class="pull-right">{{ $productsData['invoice_uah_sum'] }}грн</b></td>
            </tr>
            </tbody>
        </table>

    @include('content.cart.shopping_cart.parts.cart_list_navigation')

</div>
