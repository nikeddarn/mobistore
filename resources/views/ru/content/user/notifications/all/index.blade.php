@extends('layouts/user')

@section('user_content')

    @include('content.user.notifications.all.parts.title')

    <div class="m-t-4">
        @if($userAllNotifications)
            @include('content.user.notifications.all.parts.all_notifications')
        @else
            <p class="text-indent">Нет сообщений</p>
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.notifications.all.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('menu.user_sidebar_menu', ['activeMenuItem' => 'user_notifications'])
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

                    // message wasn't read yet
                    if ($(this).hasClass('font-weight-bolder')) {

                        // mark notification as read on server
                        $.ajax({
                            url: '/user/notifications/mark/' + $(this).data('notification-id')
                        });

                        // remove font highlight class
                        $(this).removeClass('font-weight-bolder');
                    }
                });
            });
        });
    </script>
@endsection