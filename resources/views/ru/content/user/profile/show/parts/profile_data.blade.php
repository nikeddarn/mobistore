<!-- Profile Data -->
<div class="list-item-underlined">
    <ul class="nav nav-pills nav-stacked">
        <li class="list-group-item">
            <strong>Имя</strong>
            <p>{{ $userProfile->name }}</p>
        </li>
        <li class="list-group-item">
            <strong>Почта</strong>
            <p>{{ $userProfile->email }}</p>
        </li>
        <li class="list-group-item">
            <strong>Телефон</strong>
            @if($userProfile->phone)
                <p>{{ $userProfile->phone }}</p>
            @else
                <p>Не указан</p>
            @endif
        </li>
        <li class="list-group-item">
            <strong>Город</strong>
            @if(isset($userProfile->city))
                <p>{{ $userProfile->city }}</p>
            @else
                <p>Не указан</p>
            @endif
        </li>
        <li class="list-group-item">
            <strong>Сайт</strong>
            @if($userProfile->site)
                <p>
                    <a href="{{ $userProfile->site }}" target="_new">{{ $userProfile->site }}</a>
                </p>
            @else
                <p>Не указан</p>
            @endif
        </li>
    </ul>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary pull-right m-t-2">
        <i class="fa fa-pencil"></i> Редактировать профиль
    </a>
</div>
<!-- End Profile Data -->
