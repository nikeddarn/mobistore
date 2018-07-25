@extends('layouts/admin')

@section('content')

    {{-- header--}}
    <div class="row">

        {{-- title --}}
        <div class="col-sm-3">
            @include('content.vendor.shipment.parts.title')
        </div>

    </div>

    <div class="row m-t-3">

        <div class="col-sm-3">
            @include('content.vendor.shipment.parts.menu')
        </div>

        <div class="col-sm-8">

            @if(session('message'))
                <div class=" col-lg-12 m-b-4">
                    <p class="alert alert-success">{{ session('message') }}</p>
                </div>
            @endif

            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Открытые отправки</h3>
                @if($notDispatchedVendorShipments->count())
                    @include('content.vendor.shipment.parts.shipments')
                @else
                    <p class="text-center">Нет открытых отправок</p>
                @endif
            </div>

            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Создать отправку из расписания курьеров</h3>
                @include('content.vendor.shipment.parts.create_from_schedule')
            </div>

            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Создать отправку по дате</h3>
                @include('content.vendor.shipment.parts.create_by_date')
            </div>

        </div>

    </div>

@endsection

@section('styles')
    {{--date picker--}}
    <link rel="stylesheet" href="/public/css/bootstrap-datetimepicker.css">
@endsection

@section('scripts')

    {{--date picker--}}
    <script src="/public/js/bootstrap-datetimepicker.js"></script>

    <script>
        $(document).ready(function () {

            $('.date').datetimepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                minView: 2,
                startDate: new Date()
            });

        });
    </script>
@endsection