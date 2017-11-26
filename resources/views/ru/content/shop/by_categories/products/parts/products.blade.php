<!-- Title -->
<div class="row">
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">{{ $pageData['pageTitle'] }}</h4>
        </div>
    </div>
</div>

@if($products->count())
    @foreach($products as $product)
        <div class="col-xs-6 col-md-4 col-lg-3 product-wrapper">
            <div class="product-thumbnail">
                <div class="product-image-wrapper">

                    <a href="/product/{{ $product->url }}" class="product-image-link">
                        @if($product->primaryImage)
                            <img src="{{ $productImagePathPrefix . $product->primaryImage->image }}"
                                 class="product-image"/>
                        @else
                            <img src="/images/common/no_image.png" class="product-image"/>
                        @endif
                        <div class="product-options">
                            <a href="#" data-toggle="tooltip" title="Добавить в корзину"><i
                                        class="fa fa-shopping-cart"></i></a>
                            <a href="#" data-toggle="tooltip" title="Добавить в избранное"><i class="fa fa-star-o"></i></a>
                        </div>
                    </a>

                </div>
                <div class="product-title">
                    <a href="/product/{{ $product->url }}">
                        <h5>{{ $product->page_title }}</h5>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <h3 class="text-gray text-center">Нет продуктов в данной категории</h3>
@endif