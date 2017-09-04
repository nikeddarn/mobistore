@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Заказы пользователя</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.order.show.breadcrumbs')
@endsection