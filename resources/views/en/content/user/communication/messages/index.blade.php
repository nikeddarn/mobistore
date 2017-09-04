@extends('layouts/user')

@section('user_content')
    <div class="underlined-title">
        <h3 class="page-header text-gray">Уведомления и чат</h3>
    </div>
@endsection

@section('breadcrumbs')
    @include('content.user.communication.messages.breadcrumbs')
@endsection