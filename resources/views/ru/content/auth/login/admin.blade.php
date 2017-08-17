@extends('layouts.admin')

@section('content')

    <div class="container">

        <div class="row">

            <div class="col-sm-4">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Вход администратора</h3>
                </div>

                <!-- Login Form -->
                @include('content.auth.login.parts.admin_form')

            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! $loginFormValidator->selector('#login-form') !!}
@endsection