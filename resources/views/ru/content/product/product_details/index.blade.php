@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.product.product_details.parts.breadcrumbs')

    <div class="container m-t-2">
        <div class="row">

            {{--images--}}
            <div class="col-sm-4">
                @include('content.product.product_details.parts.images')
            </div>

            {{--details--}}
            <div class="col-sm-8">
                @include('content.product.product_details.parts.details')
            </div>

            {{--tabs--}}
            <div id="product-detail-tabs" class="col-sm-8">
                @include('content.product.product_details.parts.tabs')
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

@section('styles')
    <link rel="stylesheet" href="/public/css/jquery.bootstrap-touchspin.css"><link>
    @endsection

@section('scripts')
    <script src="/public/js/jquery.bootstrap-touchspin.js"></script>
    <script src="/public/js/bootstrap-rating.min.js"></script>

    <script>

        $(document).ready(function () {

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            $('#product-detail-quantity').TouchSpin({
                verticalbuttons: true,
                prefix: 'qty'
            });

        });
    </script>
@endsection