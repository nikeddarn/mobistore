@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Личные данные пользователя</h3>
    </div>

    <div class="m-t-4">
        @include('content.user.profile.show.profile_data')
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.profile.show.breadcrumbs')
@endsection