@extends('layouts/user')

@section('user_content')

    <div class="underlined-title">
        <h3 class="page-header text-gray">Редактирование личных данных</h3>
    </div>

    <div class="m-t-4">
        @include('content.user.profile.change.profile_form')
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.profile.change.breadcrumbs')
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! $profileFormValidator->selector('#change-profile-form') !!}
@endsection