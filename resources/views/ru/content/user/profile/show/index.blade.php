@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Личные данные пользователя</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.profile.show.breadcrumbs')
@endsection