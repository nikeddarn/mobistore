@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_categories.products.parts.breadcrumbs')

    <div class="container">


        <div class="row">

        @if($filters)
            <!-- Filters -->
                <div class="col-xs-12 col-sm-3">

                    @if(isset($filters['brand']))
                        @include('content.shop.by_categories.products.parts.brands')
                    @endif

                    @if(isset($filters['model']))
                        @include('content.shop.by_categories.products.parts.models')
                    @endif

                    @if(isset($filters['quality']))
                        @include('content.shop.by_categories.products.parts.quality')
                    @endif

                    @if(isset($filters['color']))
                        @include('content.shop.by_categories.products.parts.colors')
                    @endif

                </div>
        @endif

        <!-- Products -->
            <div id="products-list"
                 class="col-xs-12 col-sm-9 @if(!$filters) col-sm-offset-1 @endif">
                    @include('content.shop.by_categories.products.parts.products')

                @if(isset($productsPagesLinks))

                    {{ $productsPagesLinks }}

                    <ul class="pagination">
                        <li>
                            <a href="{{ $viewAllUrl }}">смотреть все</a>
                        </li>
                    </ul>

                @endif

            </div>

        </div>

    @if($isPageFirstOrSingle && $pageData['summary'])

        <!-- Category summary -->
            <div class="row m-t-4">
                @include('content.shop.by_categories.products.parts.summary')
            </div>

        @endif

    </div>

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
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection