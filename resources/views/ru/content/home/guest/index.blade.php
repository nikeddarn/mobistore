@extends('layouts/common')

@section('content')

    <div class="container">

        <!-- Marketing Icons Section -->
    @include('content.home.guest.marketing')

    <!-- Portfolio Section -->
    {{--    @include('pages/main/parts/portfolio')--}}

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
        $('.carousel').carousel({
            interval: 5000 //changes the speed
        })
    </script>
@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.home') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.home') }}</title>
@endsection