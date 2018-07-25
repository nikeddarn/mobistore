<div id="userNotifications" class="table-responsive">
    <table class="table user-layout-table">

        <thead>

        <tr>
            <td colspan="2" class="text-center">
                <span class="user-layout-table-header">Все сообщения пользователя</span>
                <a class="pull-right text-gray" href="{{ route('user_notifications.show.unread') }}">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    <span>Показать актуальные</span>
                </a>
            </td>
        </tr>

        <tr class="table-row-separator"></tr>

        </thead>

        <tbody>

        @foreach($userAllNotifications['notifications'] as $notification)

            <tr class="item-details-toggle @if(!$notification['wasRead']) font-weight-bolder @endif" data-notification-id="{{ $notification['id'] }}"
                data-toggle="collapse"
                data-target="#notification-{{ $notification['id'] }}">

                <td class="user-invoice-date text-center">{{ $notification['createdAt'] }}</td>

                <td class="text-gray">
                    <span>{{ $notification['title'] }}</span>
                    <span class="item-details-pointer glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span>
                </td>

            </tr>

            <tr class="user-invoice-details">

                <td colspan="2">

                    <div id="notification-{{ $notification['id'] }}" class="collapse">

                        <div class="user-notification-details-content">

                            <p class="text-left text-indent">{!! $notification['message'] !!}</p>

                        </div>

                    </div>

                </td>

            </tr>

        @endforeach

        </tbody>
    </table>
</div>

@if(isset($userAllNotifications['links']))
    <div>
        <span class="col-sm-offset-1">{{ $userAllNotifications['links'] }}</span>
    </div>
@endif