<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target="#top-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
</div>

<!-- Collect the nav links, forms, and other content for toggling -->
<div class="collapse navbar-collapse" id="top-navbar-collapse">

    <ul class="nav navbar-nav navbar-right">

        <li>
            <a href="/about">О нас</a>
        </li>
        <li>
            <button  class="btn btn-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Оптовым покупателям&nbsp;<b
                        class="caret"></b>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a href="/wholesale">Продажа оптом</a>
                </li>
                <li>
                    <a href="/partner">Доставка от партнеров</a>
                </li>
                <li>
                    <a href="/manufacturer">Доставка под заказ</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="/warranty">Условия гарантии</a>
        </li>
        <li>
            <a href="/contact">Контакты</a>
        </li>

        <li>
            @if(auth('web')->check())
                @include('headers.common.top.parts.user_menu')
            @else
                <a href="{{ route('login') }}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Войти</a>
            @endif
        </li>

    </ul>

</div>
<!-- /.navbar-collapse -->