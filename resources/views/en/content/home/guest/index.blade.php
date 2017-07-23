@extends('layouts/common')

@section('content')

    <!-- Store Navbar -->
    @include('menu.store_navbar')

    <!-- Carousel -->
    @include('carousels.header_carousel')

    <div class="container">

        <!-- Services Section -->
    @include('content.home.guest.services')

    <!-- Features Section -->
        @include('content.home.guest.features')

        <hr>

        <!-- Call to Action Section -->
        @include('content.home.guest.action')

        <hr>

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