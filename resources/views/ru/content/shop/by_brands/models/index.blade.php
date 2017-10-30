@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.by_brands.models.parts.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $metaData->page_title }}</h3>
                </div>
            </div>
        </div>

        <!-- Series with Models-->
        <div id="models-of-brand" class="row">
            @include('content.shop.by_brands.models.parts.models')
        </div>

        <!-- Category summary -->
        @if($metaData->summary)
            <div class="row m-t-4">
                @include('content.shop.by_brands.models.parts.summary')
            </div>
        @endif

    </div>

@endsection

@section('meta_data')

@endsection