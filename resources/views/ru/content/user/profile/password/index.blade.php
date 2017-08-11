@extends('layouts/user')

@section('user_content')

    <div class="underlined-title">
        <h3 class="page-header text-gray">Изменение пароля</h3>
    </div>

    <div class="m-t-4">
        @include('content.user.profile.password.change_password_form')
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.profile.password.breadcrumbs')
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! $changePasswordFormValidator->selector('#change-password-form') !!}
@endsection