@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.categories.parts.breadcrumbs')

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">{{ $metaData->page_title_ru }}</h3>
                </div>
            </div>
        </div>

        <!-- Children categories -->
        <div id="children-categories" class="row m-t-4">
            @include('content.shop.categories.parts.categories')
        </div>

        <!-- Category summary -->
        @if($metaData->summary_ru)
            <div class="row m-t-4">
                @include('content.shop.categories.parts.summary')
            </div>
        @endif

    </div>

@endsection

@section('meta_data')

@endsection