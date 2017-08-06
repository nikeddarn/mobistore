@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Гарантийное обслуживание пользователя</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.warranty.show.breadcrumbs')
@endsection