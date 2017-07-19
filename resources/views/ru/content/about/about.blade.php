@extends('layouts/common')

@section('content')

    <!-- Page Content -->
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Обо мне
                    <small>Как я стал реабилитологом</small>
                </h1>

                <!-- Page Heading/Breadcrumbs -->
                @include('pages/about/parts/breadcrumbs')

            </div>
        </div>

        <!-- Intro Content -->
        @include('pages/about/parts/intro')

    <!-- Diploma -->
    @include('pages/about/parts/diploma')

        <!-- Team Members -->
{{--        @include('pages/about/parts/team')--}}

        <!-- Our Customers -->
{{--        @include('pages/about/parts/customers')--}}

        <hr>

    </div>
@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.about') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.about') }}</title>
@endsection