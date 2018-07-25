@extends('layouts/admin')

@section('content')

    {{-- header--}}
    <div class="row">

        {{-- title --}}
        <div class="col-sm-3">
            @include('content.vendor.courier.parts.title')
        </div>

    </div>

    <div class="row m-t-3">

        <div class="col-sm-3">
            @include('content.vendor.courier.parts.menu')
        </div>

        <div class="col-sm-8">

            @if(session('message'))
                <div class=" col-lg-12 m-b-4">
                    <p class="alert alert-success">{{ session('message') }}</p>
                </div>
            @endif

            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Ближайшие туры</h3>
                @if($courierTours->count())
                    @include('content.vendor.courier.parts.tours')
                @else
                    <p class="text-center">Нет записей</p>
                @endif
            </div>


            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Добавить курьера</h3>
                @include('content.vendor.courier.parts.create_courier')
            </div>

            <div class=" col-lg-12 m-b-4">
                <h3 class="text-center text-gray m-b-4">Добавить тур курьера</h3>
                @include('content.vendor.courier.parts.create_courier_tour')
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