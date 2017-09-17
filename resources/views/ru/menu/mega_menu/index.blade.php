<ul class="dropdown-menu">
    <li>
        <div class="yamm-content">
            <div class="row">
                <nav id="categoties" class="grid">
                    @foreach($categories[0]->children as $category)
                        <div class="col-xs-12 col-sm-3 col-md-2 grid-item m-b-2">
                            <a href="#" class="lead">
                                <h4>
                                    {{ $category->title_ru }}&nbsp;
                                    <i class="small fa fa-caret-right" aria-hidden="true"></i>
                                </h4>
                            </a>
                            <ul class="list-unstyled hidden-xs">
                                @each('menu.mega_menu.subcategories', $category->children, 'category')
                            </ul>
                        </div>
                    @endforeach
                </nav>
                <nav id="brands">
                    <ul class="list-unstyled">
                        @foreach($brands as $brand)
                            <li>
                                <div class="col-xs-12 visible-xs">
                                    <a href="#"><h4>{{ $brand->title }}</h4></a>
                                </div>
                                <div class="col-xs-6 col-sm-2 hidden-xs">
                                    <a href="#" class="thumbnail">
                                        <img src="{{ $brand->image }}" class="img-responsive"
                                             style="height: 40px">
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </li>
</ul>