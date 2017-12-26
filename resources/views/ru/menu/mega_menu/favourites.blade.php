<div class="container-fluid">
    <div class="row row-flex">
        @if($allFavourites)

            @foreach($allFavourites as $product)

                <div class="col-xs-6 col-sm-4 col-md-3 col-lg-15 product-wrapper m-b-2">

                    <div class="product-thumbnail">

                        <div class="product-image-wrapper">
                            <a href="/product/{{ $product->url }}" class="product-image-link">

                                @if($product->image)
                                    <img src="{{ $product->image }}"
                                         class="product-image"/>
                                @else
                                    <img src="/images/common/no_image.png" class="product-image"/>
                                @endif

                                <div class="product-options">
                                    <a href="#" data-toggle="tooltip" title="Добавить в корзину"><i
                                                class="fa fa-shopping-cart"></i></a>

                                    <a href="/favourite/remove/{{ $product->id }}" class="product-favourite"
                                       data-toggle="tooltip" title="Удалить из избранного">
                                        <i class="fa fa-star"></i>
                                    </a>

                                </div>

                            </a>
                        </div>

                        <div class="product-title">
                            <a href="/product/{{ $product->url }}">
                                <h5>{{ $product->page_title }}</h5>
                            </a>
                        </div>

                        @if($product->price)
                            <div class="product-price">
                                <span>{{ $product->priceUah }}&nbsp;грн</span>
                                <span class="pull-right">${{ $product->price }}</span>
                            </div>
                        @endif

                        <div class="product-stock-status">
                            @if($product->stockStatus === 1)
                                <span class="text-primary">Готов к отгрузке</span>
                            @elseif($product->stockStatus === 0)
                                <span class="text-warning">Под заказ (2-5 дней)</span>
                            @elseif($product->stockStatus === null)
                                <span class="text-danger">Нет в наличии</span>
                            @endif
                        </div>

                    </div>

                </div>

            @endforeach
        @else
            <h3 class="text-gray text-center">Нет продуктов в данной категории</h3>
        @endif
    </div>
</div>