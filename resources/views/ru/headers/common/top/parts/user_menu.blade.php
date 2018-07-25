<div id="user-control-menu-header">
    <ul class="nav navbar-nav dropdown pull-left">
        <li class="dropdown-toggle">

            <button class="btn btn-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-user"></i>&nbsp;
                <span>{{ $userData['userName'] }}</span>&nbsp;
                @if($userData['userBadges']['count'])
                    <span class="badge badge-info">{{ $userData['userBadges']['count'] }}</span>&nbsp;
                @endif
                <span class="caret"></span>
            </button>

            @include('menu.user_control_menu')

        </li>
    </ul>
</div>