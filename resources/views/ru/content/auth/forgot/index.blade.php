@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.auth.forgot.breadcrumbs')

    <div class="container">

        <div class="row">

            <div class="col-sm-8">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Запрос на восстановления пароля</h3>
                </div>

                <!-- Login Form -->
                @include('content.auth.forgot.forgot_form')

            </div>

        </div>

    </div>

@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.login') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.login') }}</title>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! $forgotFormValidator->selector('#forgot-form') !!}
@endsection