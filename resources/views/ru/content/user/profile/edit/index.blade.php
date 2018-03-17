@extends('layouts/user')

@section('user_content')

    @include('content.user.profile.edit.parts.title')

    <div class="m-t-3">
        @include('content.user.profile.edit.parts.profile_form')
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.profile.edit.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('content.user.profile.edit.parts.user_menu')
@endsection

@section('meta_data')

    <title>{{ $commonMetaData['title'] }}</title>

    @if(isset($commonMetaData['description']))
        <meta name="description" content="{{ $commonMetaData['description'] }}">
    @endif

    @if(isset($commonMetaData['keywords']))
        <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
    @endif

@endsection