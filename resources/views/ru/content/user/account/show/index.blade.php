@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Аккаунт пользователя</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.account.show.breadcrumbs')
@endsection