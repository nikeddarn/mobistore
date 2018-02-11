<div class="col-sm-4 col-md-3 hidden-xs">
    <div id="dropdown-cart" class="header-middle-item">
        <button type="button"
                class="btn btn-default dropdown-toggle pull-right header-middle-item-height" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="true">
            <i class="fa fa-shopping-cart"></i>
            <span>&nbsp;Корзина&nbsp;:&nbsp;
                @if(isset($cartProducts['productsCount']))
                    {{ $cartProducts['productsCount'] }}
                @else
                    нет продуктов
                @endif
            </span>
            <i class="fa fa-caret-down"></i>
        </button>

        @if(!empty($cartProducts['products']))

            <div class="dropdown-menu dropdown-menu-right scrollable-menu" aria-labelledby="dropdown-cart">

                @foreach($cartProducts['products'] as $product)

                    <div class="media">
                        <div class="media-left">
                            <a href="{{ $product['url'] }}">
                                <img class="media-object img-thumbnail" src="{{ $product['image'] }}" alt="product">
                            </a>
                        </div>
                        <div class="media-body">
                            <a href="{{ $product['url'] }}" class="media-heading">{{ $product['title'] }}</a>
                            <div class="product-price">x&nbsp;{{ $product['quantity'] }}&emsp;${{ $product['price'] }}</div>
                        </div>
                        <div class="media-right">
                            <a href="{{ route('cart.remove', ['id' => $product['id']]) }}" data-toggle="tooltip"
                               title="Удалить">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>

                @endforeach

                <hr>
                <div>Итого:&nbsp;<span class="product-price">${{ $cartProducts['totalSum'] }}</span></div>
                <hr>
                <div class="text-center">
                    <div class="btn-group" role="group" aria-label="View Cart and Checkout Button">
                        <a href="{{ route('cart.show') }}" class="btn btn-default btn-sm" type="button">
                            <i class="fa fa-shopping-cart"></i>
                            Корзина
                        </a>
                        <a href="{{ route('checkout.show') }}" class="btn btn-default btn-sm" type="button">
                            <i class="fa fa-check"></i>
                            Оформление
                        </a>
                    </div>
                </div>
            </div>

        @endif

    </div>
</div>