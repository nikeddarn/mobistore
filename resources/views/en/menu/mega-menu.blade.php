<ul class="dropdown-menu">
    <li>
        <div class="mega-menu-content yamm-content">
            <div class="row">
                <div class="col-sm-3">
                    <ul class="list-unstyled">
                        <li><p><strong>Категории товаров</strong></p></li>
                        @foreach($categories as $category)
                            <li><a href="products.html">{{ $category->title_ru }}</a></li>
                        @endforeach
                    </ul>
                    <ul class="list-unstyled">
                        <li><p><strong>Бренды</strong></p></li>
                        @foreach($brands as $brand)
                            <li><a href="products.html">{{ $brand->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-9">
                    <div class="thumbnail blog-list">
                        <a href="detail.html"><img src="/images/brands/apple.png" alt=""></a>
                        <div class="caption">
                            <h4>Lorem ipsum dolor sit</h4>
                            <p class="visible-lg">Lorem ipsum dolor sit amet, consectetur
                                adipisicing.</p>
                            <div class="text-right"><a href="detail.html"
                                                       class="btn btn-theme btn-sm"><i
                                            class="fa fa-long-arrow-right"></i> More</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>