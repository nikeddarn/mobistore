<div id="top-navbar" class="navbar-small">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container">

            <!-- Language picker -->
        @include('headers.common.language')

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

                <ul id="header-top-navbar-login" class="nav navbar-nav navbar-right">
                    <li>
                        @if(Auth::check())
                            <a href="/logout"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;Выйти</a>
                        @else
                            <a href="/login"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Личный
                                кабинет</a>
                        @endif
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="/about">О нас</a>
                    </li>
                    <li>
                        <a href="/massage/common" class="dropdown-toggle" data-toggle="dropdown">Оптовым покупателям<b
                                    class="caret"></b></a>
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
                </ul>

            </div>
            <!-- /.navbar-collapse -->

        </div>
        <!-- /.container -->
    </nav>
</div>