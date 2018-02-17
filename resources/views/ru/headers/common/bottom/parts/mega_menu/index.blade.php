<ul class="dropdown-menu">
    <li>
        <div class="yamm-content">

            <div class="row">

                <div id="product-path-selection" class="col-sm-3 m-b-2">
                    <ul class="list-unstyled">
                        <li><a href="{{ route('product.category') }}" class="product-path-selected" data-target="product-path-categories"> По категории</a></li>
                        <li><a href="{{ route('product.brand') }}" data-target="product-path-brands"> По бренду</a></li>
                        <li><a href="{{ route('product.favourite') }}" data-target="product-path-favourites"> Фаворитные</a></li>
                        <li><a href="{{ route('product.recent') }}" data-target="product-path-recent"> Недавние</a></li>
                        <li><a href="{{ route('product.action') }}" data-target="product-path-action"> Акционные</a></li>
                    </ul>
                </div>

                <div id="product-path-content" class="col-sm-9 scrollable-menu">

                    <div id="product-path-categories" class="row">
                    @include('headers.common.bottom.parts.mega_menu.parts.categories')
                    </div>

                    <div id="product-path-brands" class="row hidden">
                    @include('headers.common.bottom.parts.mega_menu.parts.brands')
                    </div>

                    <div id="product-path-favourites" class="row hidden products-list">
                    @include('headers.common.bottom.parts.mega_menu.parts.favourites')
                    </div>

                    <div id="product-path-recent" class="row hidden products-list">
                    @include('headers.common.bottom.parts.mega_menu.parts.recent')
                    </div>

                    <div id="product-path-action" class="row hidden products-list">
                    @include('headers.common.bottom.parts.mega_menu.parts.action')
                    </div>

                </div>

            </div>

        </div>
    </li>
</ul>