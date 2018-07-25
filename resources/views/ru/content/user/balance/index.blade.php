@extends('layouts/user')

@section('user_content')

    @include('content.user.balance.parts.title')

    <div id="user-account-balance" class="m-t-3">
        @include('content.user.balance.parts.balance')
    </div>

    <div class="m-t-3">
        @if($userInvoices)
            @include('content.user.balance.parts.invoices')
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.balance.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('menu.user_sidebar_menu', ['activeMenuItem' => 'user_balance'])
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

@section('scripts')
    <script>
        $(document).ready(function () {

            // iterate each user details toggle
            $('.item-details-toggle').each(function () {

                // set onClick event
                $(this).click(function () {

                    // toggle item details pointer class
                    $(this).find('.item-details-pointer').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
                });
            });
        });
    </script>
@endsection