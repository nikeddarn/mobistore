@extends('layouts/common')

@section('content')

    <!-- Carousel -->
{{--    @include('carousels.header_carousel')--}}

    <div class="container">

        <div class="row">

            <!-- User Header -->
            <div id="user-header" class="col-sm-4 col-md-3">

                @include('headers.user.index')

            </div>

            <!-- User Navbar -->
            <div id="user-navbar" class="col-sm-8 col-md-9">

                @include('menu.user_navbar_menu')

            </div>


        </div>

    </div>

@endsection

@section('scripts')
    <!-- Script to Activate the Carousel -->
    <script>
        // activate carousel
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 5000 //change the speed
            });
        });
    </script>
@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.home') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.home') }}</title>
@endsection