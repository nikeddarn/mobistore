@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.auth.login.parts.breadcrumbs')

    <div class="container">

        <div class="row">

            <div class="col-sm-4">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Вход пользователя</h3>
                </div>

                <!-- Login Form -->
                @include('content.auth.login.parts.login_form')

            </div>

            <div class="col-sm-8">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Нет аккаунта? Зарегистрируйтесь</h3>
                </div>

                <!-- Registration Form -->
                @include('content.auth.login.parts.registration_form')

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
    {!! $loginFormValidator->selector('#login-form') !!}
    {!! $registrationFormValidator->selector('#registration-form') !!}
@endsection