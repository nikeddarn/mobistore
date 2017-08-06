@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.auth.reset.breadcrumbs')

    <div class="container">

        <div class="row">

            <div class="col-sm-8">
                <div class="underlined-title">
                    <h3 class="page-header text-gray">Восстановления пароля</h3>
                </div>

                <!-- Login Form -->
                @include('content.auth.reset.reset_form')

            </div>

        </div>

    </div>

@endsection

@section('description')
    <meta name="description" content="{{ trans('meta.description.reset') }}">
@endsection

@section('title')
    <title>{{ trans('meta.title.reset') }}</title>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
    {!! $resetFormValidator->selector('#reset-form') !!}
@endsection