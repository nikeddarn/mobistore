@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.products.parts.breadcrumbs')

    <div class="container">

        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $metaData->page_title_ru }}</h3>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Filters -->
            <div class="col-xs-12 col-sm-3">

            </div>

            <!-- Products -->
            <div id="products-list" class="col-xs-12 col-sm-9">
                <div class="row">
                    @include('content.shop.products.parts.products')
                </div>
            </div>

        </div>

        <!-- Category summary -->
        @if($metaData->summary_ru)
            <div class="row m-t-4">
                @include('content.shop.products.parts.summary')
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