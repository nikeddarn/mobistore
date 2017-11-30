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
    <link rel="stylesheet" href="/public/css/jquery.bootstrap-touchspin.css">
    <link rel="stylesheet" href="/public/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/public/css/owl.theme.default.min.css">
@endsection

@section('scripts')
    <script src="/public/js/jquery.bootstrap-touchspin.js"></script>
    <script src="/public/js/bootstrap-rating.min.js"></script>
    <script src="/public/js/owl.carousel.min.js"></script>
    <script src="/public/js/jquery.ez-plus.js"></script>

    <script>

        $(document).ready(function () {

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });

            $('#product-detail-quantity').TouchSpin({
                verticalbuttons: true,
                min: 1,
                prefix: 'qty'
            });

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

            let zoomImage = $('#zoom-image');

            $('#product-images').find('.owl-carousel a').click(function (event) {
                event.stopPropagation();
                event.preventDefault();

                let newImageSrc =  event.target.getAttribute('src');

                zoomImage.attr('src', newImageSrc);
                zoomImage.data('zoom-image', newImageSrc).ezPlus({
                    zoomWindowOffsetX: 30,
                    zoomWindowWidth: $('#product-description').width(),
                    zoomWindowHeight: zoomImage.height(),
                });

            });

            zoomImage.ezPlus({
                zoomWindowOffsetX: 30,
                zoomWindowWidth: $('#product-description').width(),
                zoomWindowHeight: zoomImage.height(),
            });

            // trigger 'click' event on anchor with href that is same to url hash tag.
            let urlHashTag = window.location.hash;
            if(urlHashTag){
                $('a[href="'+urlHashTag+'"]').trigger('click');
            }

        });
    </script>
@endsection