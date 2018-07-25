@extends('layouts/admin')

@section('content')

    {{-- header--}}
    <div class="row">

        {{-- title --}}
        <div class="col-sm-3">
            @include('content.storage.incoming.parts.title')
        </div>

    </div>

    <div class="row m-t-3">

        <div class="col-sm-3">
            @include('content.storage.incoming.parts.menu')
        </div>

        <div class="col-sm-8">

            @if($incomingShipments->count() || $incomingInvoices->count())

                @if($incomingShipments->count())
                    @foreach($incomingShipments as $shipment)
                    <div class=" col-lg-12 m-b-4">
                        @include('content.storage.incoming.parts.shipment')
                    </div>
                    @endforeach
                @endif

                @if($incomingInvoices->count())
                    <div class=" col-lg-12 m-b-4">
                        @include('content.storage.incoming.parts.invoice')
                    </div>
                @endif

            @else

                <h4 class="text-gray text-center">Нет входящих</h4>

            @endif

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