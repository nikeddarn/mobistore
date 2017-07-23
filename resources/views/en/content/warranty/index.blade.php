@extends('layouts/common')

@section('content')

    <!-- Store Navbar -->
    @include('menu.store_navbar')

    <!-- Breadcrumbs -->
    @include('content.warranty.breadcrumbs')

    <div class="container">



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