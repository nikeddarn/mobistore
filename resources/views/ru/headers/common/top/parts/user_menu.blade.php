<div id="header-top-user-menu">
    <ul class="nav navbar-nav dropdown pull-left">
        <li class="dropdown-toggle">

            <button class="btn btn-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-user"></i>&nbsp;
                <span>{{ $userData['userName'] }}</span>&nbsp;
                @if($userData['userBadges']['count'])
                    <span id="header-top-user-messages-count" class="badge badge-info">{{ $userData['userBadges']['count'] }}</span>&nbsp;
                @endif
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">

                <li>
                    <a href="{{ route('message.show') }}">
                        <span>Сообщения</span>&nbsp;
                        @if(isset($userData['userBadges']['badges']['message']))
                            <span class="badge badge-info">{{ $userData['userBadges']['badges']['message'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('account.show') }}">
                        <span>Аккаунт</span>&nbsp;
                        @if(isset($userData['userBadges']['badges']['account']))
                            <span class="badge badge-info">{{ $userData['userBadges']['badges']['account'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('delivery.show') }}">
                        <span>Доставки</span>&nbsp;
                        @if(isset($userData['userBadges']['badges']['delivery']))
                            <span class="badge badge-info">{{ $userData['userBadges']['badges']['delivery'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('warranty.show') }}">
                        <span>Гарантия</span>&nbsp;
                        @if(isset($userData['userBadges']['badges']['warranty']))
                            <span class="badge badge-info">{{ $userData['userBadges']['badges']['warranty'] }}</span>
                        @endif
                    </a>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="{{ route('profile.show') }}">Профиль</a>
                </li>

                <li>
                    <a href="{{ route('password.reset') }}">Сменить пароль</a>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="{{ route('logout') }}">Выйти</a>
                </li>

            </ul>
        </li>
    </ul>
</div>


{{--<ul class="dropdown nav navbar-nav">--}}
{{--<li class="dropdown-toggle" id="dropdown-available-languages" data-toggle="dropdown" aria-haspopup="true"--}}
{{--aria-expanded="true">--}}
{{--<a href="/language/ru">--}}
{{--<img src="/images/flags/ru.svg" alt="Русский" class="icon-small"><span>Русский</span>--}}
{{--<span class="caret"></span>--}}
{{--</a>--}}
{{--</li>--}}
{{--<ul class="dropdown-menu" aria-labelledby="dropdown-available-languages">--}}
{{--<li class="{{ Request::path() ==  'user/communication' ? 'active' : '' }}">--}}
{{--<a href="/user/communication"><span>Уведомления и чат</span><span class="badge badge-info pull-right">3</span></a>--}}
{{--</li>--}}
{{--<li class="{{ Request::path() ==  'user/account' ? 'active' : '' }}">--}}
{{--<a href="/user/account"><span>Аккаунт</span><span class="badge badge-error pull-right">1</span></a>--}}
{{--</li>--}}
{{--<li class="{{ Request::path() ==  'user/order' ? 'active' : '' }}">--}}
{{--<a href="/user/order"><span>Заказы</span></a>--}}
{{--</li>--}}
{{--<li class="{{ Request::path() ==  'user/delivery' ? 'active' : '' }}">--}}
{{--<a href="/user/delivery"><span>Доставки</span></a>--}}
{{--</li>--}}
{{--<li class="{{ Request::path() ==  'user/warranty' ? 'active' : '' }}">--}}
{{--<a href="/user/warranty"><span>Гарантия</span></a>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</ul>--}}