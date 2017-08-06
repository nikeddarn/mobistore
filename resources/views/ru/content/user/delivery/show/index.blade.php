@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Доставки пользователя</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.delivery.show.breadcrumbs')
@endsection