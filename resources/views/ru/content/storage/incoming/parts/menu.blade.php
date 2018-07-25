<ul class="nav nav-pills nav-stacked">

    <li>
        <a href="{{ route('storage.product', ['storageId' => $storageId]) }}">
            <span>Продукты</span>
        </a>
    </li>

    <li class="active">
        <a href="{{ route('storage.incoming', ['storageId' => $storageId]) }}">
            <span>Входящие</span>
        </a>
    </li>
    <li>
        <a href="{{ route('storage.outgoing', ['storageId' => $storageId]) }}">
            <span>Исходящие</span>
        </a>
    </li>

    <li>
        <a href="{{ route('storage.delivery', ['storageId' => $storageId]) }}">
            <span>Отгрузки</span>
        </a>
    </li>

    <li>
        <a href="{{ route('storage.shipment', ['storageId' => $storageId]) }}">
            <span>Отправки</span>
        </a>
    </li>

    <li>
        <a href="{{ route('storage.warranty', ['storageId' => $storageId]) }}">
            <span>Гарантия</span>
        </a>
    </li>

    <li>
        <a href="{{ route('storage.payment', ['storageId' => $storageId]) }}">
            <span>Оплата</span>
        </a>
    </li>

    <li class="divider"></li>

    <li>
        <a href="{{ route('logout') }}">Выйти</a>
    </li>

</ul>