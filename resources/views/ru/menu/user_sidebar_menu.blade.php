<nav>
    <ul class="nav nav-pills nav-stacked">
        <li class="{{ Request::path() ==  'user' ? 'active' : '' }}">

            @include('headers.user.index')

        </li>
        <li class="{{ Request::path() ==  'user/communication' ? 'active' : '' }}">
            <a href="/user/communication">Уведомления и чат</a>
        </li>
        <li class="{{ Request::path() ==  'user/account' ? 'active' : '' }}">
            <a href="/user/account">Аккаунт</a>
        </li>
        <li class="{{ Request::path() ==  'user/order' ? 'active' : '' }}">
            <a href="/user/order">Заказы</a>
        </li>
        <li class="{{ Request::path() ==  'user/delivery' ? 'active' : '' }}">
            <a href="/user/delivery">Доставки</a>
        </li>
        <li class="{{ Request::path() ==  'user/warranty' ? 'active' : '' }}">
            <a href="/user/warranty">Гарантия</a>
        </li>
    </ul>
</nav>