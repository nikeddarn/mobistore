@extends('layouts/user')

@section('user_content')

    @include('content.user.warranty.parts.title')

    @if($userReclamationStock->count())
        <div id="user-account-balance" class="m-t-3">
            @include('content.user.warranty.parts.reclamation_stock')
        </div>
    @endif

    <div class="m-t-3">
        @if($userWarranties->count())
            @include('content.user.warranty.parts.reclamations')
        @else
            <p class="text-indent">Нет записей</p>
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.warranty.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('content.user.warranty.parts.user_menu')
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

            // change open invoice arrow direction
            $('#userAccount').find('button').each(function () {
                $(this).click(function (event) {

                    let openItemToggle = $(event.target).closest('button');

                    $(openItemToggle).find('span').toggleClass('glyphicon-menu-down glyphicon-menu-up');
                });
            });

        });
    </script>
@endsection