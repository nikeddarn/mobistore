@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_brands.models.parts.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $pageData['pageTitle'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Series with Models-->
        <div id="models-of-brand" class="row">
            @include('content.shop.by_brands.models.parts.models')
        </div>

        <!-- Category summary -->
        @if($pageData['summary'])
            <div class="row m-t-4">
                @include('content.shop.by_brands.models.parts.summary')
            </div>
        @endif

    </div>

@endsection

@section('meta_data')
    <title>{{ $commonMetaData['title'] }}</title>
    <meta name="description" content="{{ $commonMetaData['description'] }}">
    <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
@endsection