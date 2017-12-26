<!-- Title -->
<div class="row">

    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">{{ $pageData['pageTitle'] }}</h4>
        </div>
    </div>

</div>

<div class="container-fluid">
    <div class="row row-flex">
        @if($products->count())
            @foreach($products as $product)

                <div class="col-xs-6 col-md-4 col-lg-3 product-wrapper m-b-2">

                    <div class="product-thumbnail">

                        <div class="product-image-wrapper">
                            <a href="/product/{{ $product->url }}" class="product-image-link">

                                @if($product->image)
                                    <img src="{{ $product->image }}" class="product-image"/>
                                @else
                                    <img src="/images/common/no_image.png" class="product-image"/>
                                @endif

                                @if($product->badges)
                                    <div class="product-badges">
                                        @foreach($product->badges as $badge)
                                            <span class="badge-label">
                                                <span class="label label-arrow label-arrow-left label-{{ $badge['class'] }}">{{ $badge['title'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($product->stockStatus !== null)

                                    <div class="product-options">
                                        <a href="#" data-toggle="tooltip" title="Добавить в корзину"><i
                                                    class="fa fa-shopping-cart"></i></a>

                                        @if($product->isFavourite)
                                            <a href="/favourite/remove/{{ $product->id }}" class="product-favourite"
                                               data-toggle="tooltip" title="Удалить из избранного">
                                                <i class="fa fa-star"></i>
                                            </a>
                                        @else
                                            <a href="/favourite/add/{{ $product->id }}" class="product-not-favourite"
                                               data-toggle="tooltip" title="Добавить в избранное">
                                                <i class="fa fa-star"></i>
                                            </a>
                                        @endif

                                    </div>
                                @endif

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
