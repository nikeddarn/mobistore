@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.product.product_details.parts.breadcrumbs')

    <div class="container m-t-2">
        <div class="row">

            {{--images--}}
            <div id="product-images" class="col-sm-4">
                @include('content.product.product_details.parts.images')
            </div>

            {{--details--}}
            <div id="product-description" class="col-sm-8">
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
    {{-- input quantity field styles --}}
    <link rel="stylesheet" href="/public/css/jquery.bootstrap-touchspin.css">

    {{-- product images carousel styles --}}
    <link rel="stylesheet" href="/public/css/owl.carousel.min.css">

    {{-- product main image zoom styles --}}
    <link rel="stylesheet" href="/public/css/owl.theme.default.min.css">
@endsection

@section('scripts')
    {{-- input quantity field creator--}}
    <script src="/public/js/jquery.bootstrap-touchspin.js"></script>

    {{-- input rating field creator--}}
    <script src="/public/js/bootstrap-rating.min.js"></script>

    {{-- product images carousel--}}
    <script src="/public/js/owl.carousel.min.js"></script>

    {{-- product main image zoom--}}
    <script src="/public/js/jquery.ez-plus.js"></script>

    <script>

        $(document).ready(function () {

            // pop up tooltip
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            // input quantity field creator
            $('#product-detail-quantity').TouchSpin({
                verticalbuttons: true,
                min: 1,
                prefix: 'qty'
            });

            // product images carousel
            $('.owl-carousel').owlCarousel({
                dots: false,
                nav: true,
                navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                margin: 5,
                responsive:{
                    0:{
                        items:2,
                    },
                    768:{
                        items:3,
                    },
                    1200:{
                        items:4,
                    }
                }
            });

            // product main image zoom
            let zoomImage = $('#zoom-image');
            let activateZoom = function activateZoom(zoomImage){
                zoomImage.ezPlus({
                    zoomWindowOffsetX: 30,
                    zoomWindowWidth: $('#product-description').width(),
                    zoomWindowHeight: zoomImage.height(),
                });
            };
            activateZoom(zoomImage);
            $('#product-images').find('.owl-carousel a').click(function (event) {
                event.stopPropagation();
                event.preventDefault();
                let newImageSrc =  event.target.getAttribute('src');
                activateZoom(zoomImage.attr('src', newImageSrc).data('zoom-image', newImageSrc));
            });
        });
    </script>
@endsection