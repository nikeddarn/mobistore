<ul class="dropdown-menu">
    <li>
        <div class="yamm-content">

            <div class="row " id="product-path-selection">

                <div class="col-sm-4 text-center">
                    <a href="/category" class="product-path-link product-path-active-link"
                       data-target="product-path-categories">Выбрать товары по категории</a>
                </div>

                <div class="col-sm-4 text-center">
                    <a href="/brand" class="product-path-link" data-target="product-path-brands">Выбрать товары по
                        бренду</a>
                </div>

                <div class="col-sm-2 text-center">
                    <a href="/favourite" class="product-path-link"
                       data-target="product-path-favourites">Фаворитные</a>
                </div>

                <div class="col-sm-2 text-center">
                    <a href="/recent" class="product-path-link" data-target="product-path-recent">Недавние</a>
                </div>
            </div>

            <div id="product-path-categories" class="row m-t-3 product-path-block">
                @include('menu.mega_menu.parts.categories')
            </div>

            <div id="product-path-brands" class="row product-path-block m-t-3 hidden">
                @include('menu.mega_menu.parts.brands')
            </div>

            <div id="product-path-favourites" class="row product-path-block m-t-3 hidden products-list">
                @include('menu.mega_menu.parts.favourites')
            </div>

            <div id="product-path-recent" class="row product-path-block m-t-3 hidden products-list">
                @include('menu.mega_menu.parts.recent')
            </div>

        </div>
    </li>
</ul>