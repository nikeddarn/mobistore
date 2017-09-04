<nav class="user-menu list-item-underlined">
    <ul class="nav nav-pills nav-stacked">
        <li class="{{ Request::path() ==  'user/communication' ? 'active' : '' }}">
            <a href="/user/communication"><span>Уведомления и чат</span><span class="badge badge-info pull-right">3</span></a>
        </li>
        <li class="{{ Request::path() ==  'user/account' ? 'active' : '' }}">
            <a href="/user/account"><span>Аккаунт</span><span class="badge badge-error pull-right">1</span></a>
        </li>
        <li class="{{ Request::path() ==  'user/order' ? 'active' : '' }}">
            <a href="/user/order"><span>Заказы</span></a>
        </li>
        <li class="{{ Request::path() ==  'user/delivery' ? 'active' : '' }}">
            <a href="/user/delivery"><span>Доставки</span></a>
        </li>
        <li class="{{ Request::path() ==  'user/warranty' ? 'active' : '' }}">
            <a href="/user/warranty"><span>Гарантия</span></a>
        </li>
    </ul>
</nav>