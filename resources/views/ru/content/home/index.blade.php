@extends('layouts/common')

@section('content')

    <!-- Carousel -->
    @include('carousels.header_carousel')

    <div class="container">

        <!-- Services Section -->
    @include('content.home.parts.services')

    <!-- Features Section -->
        @include('content.home.parts.features')

        <hr>

        <!-- Call to Action Section -->
        @include('content.home.parts.action')

        <hr>

    </div>

@endsection

@section('scripts')
    <!-- Script to Activate the Carousel -->
    <script>
        $(document).ready(function () {

            // activate carousel
            $('.carousel').carousel({
                interval: 5000 //change the speed
            });

            // set the same services block height
            let maxHeight = 0;
            let blocks = $('.same-height');
            $(blocks).each(function () {
                if ($(this).height() > maxHeight) {
                    maxHeight = $(this).height();
                }
            });
            $(blocks).each(function () {
                $(this).height(maxHeight);
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