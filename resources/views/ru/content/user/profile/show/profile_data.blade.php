<!-- Profile Data -->
    <div class="list-item-underlined">
        <ul class="nav nav-pills nav-stacked">
            <li class="list-group-item">
                <strong>Имя</strong>
                    <p>{{ $name }}</p>
            </li>
            <li class="list-group-item">
                <strong>Почта</strong>
                <p>{{ $email }}</p>
            </li>
            <li class="list-group-item">
                <strong>Телефон</strong>
                @if($phone)
                    <p>{{ $phone }}</p>
                @else
                    <p>Не указан</p>
                @endif
            </li>
            <li class="list-group-item">
                <strong>Город</strong>
                @if(isset($city))
                    <p>{{ $city }}</p>
                @else
                    <p>Не указан</p>
                @endif
            </li>
            <li class="list-group-item">
                <strong>Сайт</strong>
                @if($site)
                    <p><a href="{{ $site }}" target="_new">{{ $site }}</a></p>
                @else
                    <p>Не указан</p>
                @endif
            </li>
        </ul>
        <a href="/user/profile" class="btn btn-primary pull-right m-t-2"><i class="fa fa-pencil"></i> Редактировать профиль</a>
    </div>
<!-- End Profile Data -->
