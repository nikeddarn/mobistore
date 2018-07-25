<ul id="user-sidebar-menu" class="nav nav-pills nav-stacked">

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_balance') class="active" @endif>
        <a href="{{ route('user_balance.show') }}">
            <span>Баланс</span>
        </a>
    </li>

    <li class="user-notifications-menu-item @if(isset($activeMenuItem) && $activeMenuItem === 'user_notifications') active @endif">
        <a href="{{ route('user_notifications.show.unread') }}">
            <span>Сообщения</span>&nbsp;
            @if($userData['userBadges']['badges']['totalNotifications'])
                <span class="badge badge-info">{{ $userData['userBadges']['badges']['totalNotifications'] }}</span>
            @endif
        </a>
    </li>


    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_shipments') class="active" @endif>
        <a href="{{ route('user_shipments.show') }}">
            <span>Отгрузки</span>&nbsp;
            @if($userData['userBadges']['badges']['shipments'])
                <span class="badge badge-info">{{ $userData['userBadges']['badges']['shipments'] }}</span>
            @endif
        </a>
    </li>

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_orders') class="active" @endif>
        <a href="{{ route('user_orders.show') }}">
            <span>Заказы</span>&nbsp;
            @if($userData['userBadges']['badges']['orders'])
                <span class="badge badge-info">{{ $userData['userBadges']['badges']['orders'] }}</span>
            @endif
        </a>
    </li>

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_reclamations') class="active" @endif>
        <a href="{{ route('user_reclamations.show') }}">
            <span>Гарантия</span>&nbsp;
            @if($userData['userBadges']['badges']['reclamations'])
                <span class="badge badge-info">{{ $userData['userBadges']['badges']['reclamations'] }}</span>
            @endif
        </a>
    </li>

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_payments') class="active" @endif>
        <a href="{{ route('user_payments.show') }}">
            <span>Платежи</span>&nbsp;
            @if($userData['userBadges']['badges']['payments'])
                <span class="badge badge-info">{{ $userData['userBadges']['badges']['payments'] }}</span>
            @endif
        </a>
    </li>

    <li class="divider"></li>

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_profile') class="active" @endif>
        <a href="{{ route('user_profile.show') }}">Профиль</a>
    </li>

    <li @if(isset($activeMenuItem) && $activeMenuItem === 'user_password') class="active" @endif>
        <a href="{{ route('user_password.reset') }}">Сменить пароль</a>
    </li>

    <li class="divider"></li>

    <li>
        <a href="{{ route('logout') }}">Выйти</a>
    </li>

</ul>