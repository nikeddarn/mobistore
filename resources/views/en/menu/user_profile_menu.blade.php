<nav class="user-menu list-item-underlined">
    <ul class="nav nav-pills nav-stacked">
        <li class="{{ Request::path() ===  'user' || Request::path() ===  'user/profile' ? 'active' : '' }}">
            <a href="/user">Профиль пользователя</a>
        </li>
        <li class="{{ Request::path() ===  'user/settings' ? 'active' : '' }}">
            <a href="/user/settings">Настройки</a>
        </li>
        <li class="{{ Request::path() ===  'user/password' ? 'active' : '' }}">
            <a href="/user/password">Сменить пароль</a>
        </li>
    </ul>
</nav>