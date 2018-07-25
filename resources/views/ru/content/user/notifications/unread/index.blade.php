@extends('layouts/user')

@section('user_content')

    @include('content.user.notifications.unread.parts.title')

    <div class="m-t-4">
        @if($userUnreadNotifications)
            @include('content.user.notifications.unread.parts.unread_notifications')
        @else
            <p class="text-indent">Нет сообщений</p>
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.notifications.unread.parts.breadcrumbs')
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

                        // collection of badges that is related with user notifications
                        let userBadges = $('#user-control-menu-header').find('.badge').add($('#user-control-menu').find('.badge')).add($('#user-sidebar-menu').find('.badge'));

                        // decrease user notifications badges value by 1, or delete badge if it's value is 0
                        $(userBadges).each(function () {
                            let badgeValue = parseInt($(this).text());
                            if (badgeValue > 1) {
                                $(this).text(badgeValue - 1);
                            } else {
                                $(this).remove();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection