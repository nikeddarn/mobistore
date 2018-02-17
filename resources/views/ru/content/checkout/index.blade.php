@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.cart.shopping_cart.parts.breadcrumbs')

    <div class="container">
        <div class="row">

            {{--Cart List--}}
            <div class="col-md-9">
                @include('content.cart.shopping_cart.parts.title')

                @include('content.cart.shopping_cart.parts.cart_list')
            </div>

            <div class="col-md-3 hidden-sm hidden-xs">

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
    {{-- input quantity field styles --}}
    <link rel="stylesheet" href="/css/jquery.bootstrap-touchspin.css">
@endsection

@section('scripts')
    {{-- input quantity field creator--}}
    <script src="/js/jquery.bootstrap-touchspin.js"></script>

    <script>
        $(document).ready(function () {

            // pop up tooltip
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            // input quantity field creator
            $('.cart-product-quantity')
                    .TouchSpin({
                    verticalbuttons: true,
                    min: 1,
                    prefix: 'qty'
                });

        });
    </script>
@endsection