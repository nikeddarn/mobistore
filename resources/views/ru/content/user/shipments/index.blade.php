@extends('layouts/user')

@section('user_content')

    @include('content.user.shipments.parts.title')

    <div class="m-t-3">
        @if($userShipments->count())
            @include('content.user.shipments.parts.shipments')
        @else
            <h4 class="text-gray text-center">Нет доставок</h4>
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.shipments.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('menu.user_sidebar_menu', ['activeMenuItem' => 'user_shipments'])
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
    <script>
        $(document).ready(function () {

            // change open invoice arrow direction
            $('#userDeliveries').find('button').each(function () {
                $(this).click(function (event) {

                    let openItemToggle = $(event.target).closest('button');

                    $(openItemToggle).find('span').toggleClass('glyphicon-menu-down glyphicon-menu-up');
                });
            });

        });
    </script>
@endsection