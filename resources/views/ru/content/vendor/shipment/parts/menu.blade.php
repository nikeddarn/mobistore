<ul class="nav nav-pills nav-stacked">

    <li>
        <a href="{{ route('vendor.account', ['vendorId' => $vendorId]) }}">
            <span>Аккаунт</span>
        </a>
    </li>

    <li>
        <a href="{{ route('vendor.order', ['vendorId' => $vendorId]) }}">
            <span>Заказы</span>
        </a>
    </li>

    <li>
        <a href="{{ route('vendor.delivery', ['vendorId' => $vendorId]) }}">
            <span>Доставки</span>
        </a>
    </li>

    <li class="active">
        <a href="{{ route('vendor.shipment', ['vendorId' => $vendorId]) }}">
            <span>Отправки</span>
        </a>
    </li>

    <li>
        <a href="{{ route('vendor.courier', ['vendorId' => $vendorId]) }}">
            <span>Курьеры</span>
        </a>
    </li>

    <li>
        <a href="{{ route('vendor.warranty', ['vendorId' => $vendorId]) }}">
            <span>Гарантия</span>
        </a>
    </li>

    <li>
        <a href="{{ route('vendor.payment', ['vendorId' => $vendorId]) }}">
            <span>Оплата</span>
        </a>
    </li>

    <li class="divider">
        <hr/>
    </li>

    <li>
        <a href="{{ route('logout') }}">Выйти</a>
    </li>

</ul>