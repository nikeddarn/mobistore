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
            <div id="products-list"
                 class="col-xs-12 col-sm-9 @if(!$filters) col-sm-offset-1 @endif">
                <div class="row">
                    @include('content.shop.by_brands.products.parts.products')
                </div>

                @if($viewAllUrl)

                    {{ $products->links() }}

                    <ul class="pagination">
                        <li>
                            <a href="{{ $viewAllUrl }}">смотреть все</a>
                        </li>
                    </ul>
                @endif

            </div>

        </div>

    @if((!method_exists($products, 'previousPageUrl') || $products->currentPage() === 1) && $metaData->summary)

        <!-- Category summary -->
            <div class="row m-t-4">
                @include('content.shop.by_categories.products.parts.summary')
            </div>

        @endif

    </div>

@endsection

@section('meta_data')

@endsection

@section('scripts')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection