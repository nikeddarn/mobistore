@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.product.product_comments.parts.breadcrumbs')

    <div class="container">
        <div id="product_comments" class="row">

            <div id="review" class="col-sm-8">

                <div class="m-b-4">
                    <div class="underlined-title">
                        <h4 class="page-header text-gray">{{ $product['title'] }} <small>Отзывы покупателей</small></h4>
                    </div>
                </div>

                @include('content.product.product_comments.parts.comments')

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