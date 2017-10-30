@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_brands.brands.parts.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $metaData->page_title }}</h3>
                </div>
            </div>
        </div>

        <!-- Brands -->
        <div id="supported-brands" class="row m-t-4">
            @include('content.shop.by_brands.brands.parts.brands')
        </div>

        <!-- Category summary -->
        @if($metaData->summary)
            <div class="row m-t-4">
                @include('content.shop.by_brands.brands.parts.summary')
            </div>
        @endif

    </div>

@endsection

@section('meta_data')

@endsection