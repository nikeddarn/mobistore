@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_brands.products.parts.breadcrumbs')

    <div class="container">


        <div class="row">

        @if($filters)
            <!-- Filters -->
                <div class="col-xs-12 col-sm-3">

                    @if(isset($filters['category']))
                        @foreach($filters['category'] as $categoriesFilter)
                            @include('content.shop.by_brands.products.parts.categories')
                        @endforeach
                    @endif

                    @if(isset($filters['quality']))
                        @include('content.shop.by_brands.products.parts.quality')
                    @endif

                    @if(isset($filters['color']))
                        @include('content.shop.by_brands.products.parts.colors')
                    @endif

                </div>
        @endif

        <!-- Products -->
            <div class="products-list col-xs-12 col-sm-9 @if(!$filters) col-sm-offset-1 @endif">
                @include('content.shop.by_brands.products.parts.products')

                @if(isset($productsPagesLinks))
                    <div class="col-lg-12">
                        {{ $productsPagesLinks }}

                        <ul class="pagination">
                            <li>
                                <a href="{{ $viewAllUrl }}">смотреть все</a>
                            </li>
                        </ul>
                    </div>
                @endif

            </div>

        @if($isPageFirstOrSingle && $pageData['summary'])

            <!-- Category summary -->
                <div class="row m-t-4">
                    @include('content.shop.by_categories.products.parts.summary')
                </div>

            @endif

        </div>

    </div>

    <!-- Modal -->
    @include('modals.favourite')

@endsection

@section('meta_data')

    <title>{{ $commonMetaData['title'] }}</title>

    @if(isset($commonMetaData['description']))
        <meta name="description" content="{{ $commonMetaData['description'] }}">
    @endif

    @if(isset($commonMetaData['keywords']))
        <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
    @endif

    @if(!empty($specialMetaData['meta']))
        @foreach($specialMetaData['meta'] as $meta)
            <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
        @endforeach
    @endif

    @if(!empty($specialMetaData['link']))
        @foreach($specialMetaData['link'] as $link)
            <link rel="{{ $link['rel'] }}" href="{{ $link['href'] }}">
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        $(function () {

            // activate tooltips
            let tooltips = $('[data-toggle="tooltip"]');
            tooltips.tooltip();

            // add and remove to favourite
            let favouriteLink = $('.product-favourite, .product-not-favourite');
            $(favouriteLink).click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                let favouriteLink = $(this);
                $.ajax({
                    url: this,
                    success: function (data) {
                        if (data !== false) {
                            let dataObject = $.parseJSON(data);
                            favouriteLink.toggleClass('product-favourite product-not-favourite');
                            favouriteLink.attr('href', dataObject.hrefReplace);
                            favouriteLink.attr('title', dataObject.title);
                            tooltips.tooltip('fixTitle');
                            showModalWindow(dataObject.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 401){
                            showModalWindow($.parseJSON(xhr.responseText).error);
                        }
                    },
                    dataType: 'json'
                });

                function showModalWindow(message) {
                    let modal = $('#favourite-product-modal');
                    $(modal).find('.modal-body p').text(message);
                    modal.modal('show');
                    let modalTimeout;
                    modal.on('shown.bs.modal', function () {
                        clearTimeout(modalTimeout);
                        modalTimeout = setTimeout(function () {
                            modal.modal('hide');
                        }, 3000)
                    });
                }

            });
        })
    </script>
@endsection