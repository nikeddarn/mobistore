<div class="product-detail-main-image">
    @if(!empty($product['images']))
        <img id="zoom-image" src="{{ $product['images'][0] }}" data-zoom-image="{{ $product['images'][0] }}">
    @else
        <img src="/public/images/common/no_image.png">
    @endif
</div>

@if(!empty($product['images']))

    <div class="owl-carousel owl-theme">

        @foreach($product['images'] as $image)

            <div class="item">
                <a href="#">
                    <img src="{{ $image }}" class="img-thumbnail">
                </a>
            </div>

        @endforeach

    </div>

@endif