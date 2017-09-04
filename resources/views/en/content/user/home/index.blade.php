@extends('layouts/common')

@section('content')

    <div class="container">

        <div class="row">

            <!-- User Header -->
            <div class="col-sm-4 col-md-3">

                <div class="m-t-4">

                @include('headers.user.index')
                @include('menu.user_profile_menu')

                </div>

            </div>

            <!-- User Navbar -->
            <div class="col-sm-8 col-md-9">

                <div class="col-sm-10 col-sm-offset-1">
                    <div class="underlined-title m-b-4">
                        <h3 class="page-header text-gray">Личный кабинет</h3>
                    </div>

                    @include('menu.user_control_menu')

                </div>
            </div>


        </div>

    </div>

@endsection

@section('scripts')

@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.home') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.home') }}</title>
@endsection