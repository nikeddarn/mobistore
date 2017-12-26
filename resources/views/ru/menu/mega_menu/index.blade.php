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
                    <a href="#" class="product-path-link">Недавние</a>
                </div>
            </div>

            <div id="product-path-categories" class="row m-t-3 product-path-block">
                <div class="grid">
                    @foreach($allCategories as $category)
                        <div class="col-xs-12 col-sm-3 col-md-2 grid-item m-b-2">
                            <a href="/category/{{ $category->url }}" class="lead">
                                <h4>
                                    {{ $category->title }}&nbsp;
                                    <i class="small fa fa-caret-right" aria-hidden="true"></i>
                                </h4>
                            </a>
                            <ul class="list-unstyled hidden-xs">
                                @each('menu.mega_menu.subcategories', $category->children, 'category')
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="product-path-brands" class="row product-path-block m-t-3 hidden">
                    @foreach($allBrands as $brand)
                    <div class="col-xs-12 col-sm-3 col-md-2 m-b-2">
                            <div class="col-xs-12 visible-xs">
                                <a href="/brand/{{ $brand->url }}"><h4>{{ $brand->title }}</h4></a>
                            </div>
                            <div class="hidden-xs">
                                <a href="/brand/{{ $brand->url }}" class="thumbnail">
                                    <img src="/images/brands/{{ $brand->image }}" class="img-responsive">
                                </a>
                            </div>
                    </div>
                    @endforeach
            </div>

            <div id="product-path-favourites" class="row product-path-block m-t-3 hidden products-list">
                @include('menu.mega_menu.favourites')
            </div>

        </div>
    </li>
</ul>