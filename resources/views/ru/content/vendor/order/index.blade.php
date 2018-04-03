@extends('layouts/admin')

@section('content')

    <div class="row">

        <div class="col-sm-3">
            @include('content.vendor.order.parts.menu')
        </div>

        <div class="col-sm-8">

            <div class="m-t-3">
                @if($outgoingOrders->count())
                    @include('content.vendor.order.parts.invoices')
                @else
                    <h4 class="text-gray text-center">Нет несобранных заказов</h4>
                @endif
            </div>

            <div class="m-t-3">
                @if($outgoingProducts->count())
                    @include('content.vendor.order.parts.outgoing_products')
                @endif
            </div>

        </div>

    </div>

@endsection

@section('styles')
    {{-- input quantity field styles --}}
    <link rel="stylesheet" href="/public/css/jquery.bootstrap-touchspin.css">
@endsection

@section('scripts')

    {{-- input quantity field creator--}}
    <script src="/public/js/jquery.bootstrap-touchspin.js"></script>

    <script>
        $(document).ready(function () {

            // change open invoice arrow direction
            $('#vendorOrders').find('button').each(function () {
                $(this).click(function (event) {

                    let openItemToggle = $(event.target).closest('button');

                    $(openItemToggle).find('span').toggleClass('glyphicon-menu-down glyphicon-menu-up');
                });
            });

            // input quantity field creator
            $('.cart-product-quantity').each(function () {
                let needingCount = $($(this).parent().parent().find('.product-needing-quantity').get(0)).text();
                $(this).TouchSpin({
                    verticalbuttons: true,
                    min: 0,
                    max: needingCount,
                    prefix: 'qty'
                });
            });


        });
    </script>
@endsection