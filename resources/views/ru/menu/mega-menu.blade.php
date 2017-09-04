<ul class="dropdown-menu light-background">
    <li>
        <div class="yamm-content">
            <div class="row">
                <div class="col-sm-3">
                    <nav class="user-menu list-item-underlined">
                        <div class="underlined-title m-b-4">
                            <h5 class="page-header text-gray m-t-0"><strong>Категории товаров</strong></h5>
                        </div>
                        {{--<p class="m-b-2"><strong>Категории товаров</strong></p>--}}
                        <ul class="nav nav-pills nav-stacked">
                            @foreach($categories as $category)
                                <li><a href="products.html">{{ $category->title_ru }}</a></li>
                            @endforeach
                        </ul>
                        {{--<ul class="nav nav-pills nav-stacked">--}}
                        {{--<li><p><strong>Бренды</strong></p></li>--}}
                        {{--@foreach($brands as $brand)--}}
                        {{--<li><a href="products.html">{{ $brand->title }}</a></li>--}}
                        {{--@endforeach--}}
                        {{--</ul>--}}
                    </nav>
                </div>
                <div class="col-sm-8 col-sm-offset-1">
                    <div class="row">
                        <nav>
                            <div class="underlined-title m-b-4">
                                <h5 class="page-header text-gray m-t-0"><strong>Бренды</strong></h5>
                            </div>
                            {{--<p class="m-b-2"><strong>Бренды</strong></p>--}}
                            <ul class="nav">
                                @foreach($brands as $brand)
                                    <li>
                                        <div class="col-xs-6 col-sm-2">
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
            </div>
        </div>
    </li>
</ul>