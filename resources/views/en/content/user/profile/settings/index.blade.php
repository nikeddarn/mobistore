@extends('layouts/user')

@section('user_content')

    <div class="underlined-title">
        <h3 class="page-header text-gray">Пользовательские настройки</h3>
    </div>

    <div class="m-t-4">
        @include('content.user.profile.settings.settings_form')
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.profile.settings.breadcrumbs')
@endsection