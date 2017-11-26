@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_brands.brands.parts.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $pageData['pageTitle'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Brands -->
        <div id="supported-brands" class="row m-t-4">
            @include('content.shop.by_brands.brands.parts.brands')
        </div>

        <!-- Category summary -->
        @if($pageData['summary'])
            <div class="row m-t-4">
                @include('content.shop.by_brands.brands.parts.summary')
            </div>
        @endif

    </div>

@endsection

@section('meta_data')
    <title>{{ $commonMetaData['title'] }}</title>
    <meta name="description" content="{{ $commonMetaData['description'] }}">
    <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
@endsection