<ul class="nav nav-pills nav-stacked">

    <li class="active">
        <a href="{{ route('message.show') }}">
            <span>Сообщения</span>
            @if(isset($userData['userBadges']['badges']['message']))
                <span class="pull-right">
                        <span id="user-menu-messages-count-pointer" class="badge badge-info pull-right">{{ $userData['userBadges']['badges']['message'] }}</span>
                    </span>
            @endif
        </a>
    </li>

    <li>
        <a href="{{ route('account.show') }}">
            <span>Аккаунт</span>
            @if(isset($userData['userBadges']['badges']['account']))
                <span class="pull-right">
                        <span class="badge badge-info pull-right">{{ $userData['userBadges']['badges']['account'] }}</span>
                    </span>
            @endif
        </a>
    </li>

    <li>
        <a href="{{ route('delivery.show') }}">
            <span>Доставки</span>
            @if(isset($userData['userBadges']['badges']['delivery']))
                <span class="pull-right">
                    <span class="badge badge-info pull-right">{{ $userData['userBadges']['badges']['delivery'] }}</span>
                    </span>
            @endif
        </a>
    </li>

    <li>
        <a href="{{ route('warranty.show') }}">
            <span>Гарантия</span>
            @if(isset($userData['userBadges']['badges']['warranty']))
                <span class="pull-right">
                    <span class="badge badge-info pull-right">{{ $userData['userBadges']['badges']['warranty'] }}</span>
                    </span>
            @endif
        </a>
    </li>

    <li class="divider"></li>

    <li>
        <a href="{{ route('profile.show') }}">Профиль</a>
    </li>

    <li>
        <a href="{{ route('password.show') }}">Сменить пароль</a>
    </li>

    <li class="divider"></li>

    <li>
        <a href="{{ route('logout') }}">Выйти</a>
    </li>

</ul>