@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.product.product_comments.parts.breadcrumbs')

    <div class="container m-t-2">
        <div id="product_comments" class="row">

            <div class="col-lg-12 m-b-4">
                <div class="underlined-title">
                    <h4 class="page-header text-gray">{{ $product['title'] }}</h4>
                </div>
            </div>

            <div id="review" class="col-sm-8">
                @include('content.product.product_comments.parts.comments')
            </div>
            <div class="col-sm-8">
                @include('content.product.product_comments.parts.create_comment')
            </div>

        </div>
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

@endsection

@section('scripts')
    {{-- input rating field creator--}}
    <script src="/public/js/bootstrap-rating.min.js"></script>
@endsection