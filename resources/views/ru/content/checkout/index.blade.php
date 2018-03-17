@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.checkout.parts.breadcrumbs')

    <div class="container">
        <div class="row">

            {{--Cart List--}}
            <div class="col-md-9">
                @include('content.checkout.parts.title')

                @if(!empty($productsData))

                    @include('content.checkout.parts.checkout_form')

                    @if(!empty($productsData['order']))
                        @include('content.checkout.parts.order')
                    @endif

                    @if(!empty($productsData['pre_order']))
                        @include('content.checkout.parts.pre_order')
                    @endif

                    @include('content.checkout.parts.control_buttons')

                @else
                    <h4 class="text-gray text-center">Ваша корзина пуста</h4>
                @endif

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

            // --------------- handle delivery type ------------------------------

            $('#deliveryType').change(function () {

                let postServiceSelect = $('#postServiceSelect');
                let userOrdersTables = $('.cart-product-list');

                if (parseInt(this.value) === 1) {
                    // courier delivery selected
                    $(postServiceSelect).addClass('hidden');
                    userOrdersTables.each(function (index) {
                        showCourierDeliveryPrices(this, index);
                    });

                } else if (parseInt(this.value) === 2) {
                    // post delivery selected
                    $(postServiceSelect).removeClass('hidden');
                    userOrdersTables.each(function (index) {
                        showPostDeliveryPrices(this, index);
                    });
                }
            });

            // property for store previous states
            showCourierDeliveryPrices.courierDeliveryText = [];
            showCourierDeliveryPrices.courierTotalSumText = [];

            function showCourierDeliveryPrices(order, index) {
                // change delivery field
                let deliverySumField = $(order).find('.invoice-delivery-uah-sum').get(0);
                $(deliverySumField).text(showCourierDeliveryPrices.courierDeliveryText[index]).removeClass('font-weight-normal').removeClass('text-italic');

                // change total sum field
                let totalSumField = $(order).find('.invoice-uah-total-sum').get(0);
                $(totalSumField).text(showCourierDeliveryPrices.courierTotalSumText[index]);
            }

            function showPostDeliveryPrices(order, index) {
                // change delivery field
                let deliverySumField = $(order).find('.invoice-delivery-uah-sum').get(0);
                showCourierDeliveryPrices.courierDeliveryText[index] = $(deliverySumField).text();
                let postDeliveryText = $(deliverySumField).data('post-delivery-text');
                $(deliverySumField).text(postDeliveryText).addClass('font-weight-normal').addClass('text-italic');

                // change total sum field
                let totalSumField = $(order).find('.invoice-uah-total-sum').get(0);
                showCourierDeliveryPrices.courierTotalSumText[index] = $(totalSumField).text();
                let invoiceSumField = $(order).find('.invoice-uah-sum').get(0);
                let invoiceSumFieldText = $(invoiceSumField).text();
                $(totalSumField).text(invoiceSumFieldText);
            }

        });
    </script>
@endsection