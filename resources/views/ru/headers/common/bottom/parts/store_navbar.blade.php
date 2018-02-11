        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed pull-left" data-toggle="collapse"
                    data-target="#store-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{ route('cart.show') }}" class="btn btn-default btn-cart-xs visible-xs pull-right">
                <i class="fa fa-shopping-cart"></i> Cart : 4 items
            </a>
        </div>
        <div class="collapse navbar-collapse pull-left" id="store-navbar-collapse">
            <ul class="nav navbar-nav dropdown yamm-fullwidth">

                <li class="dropdown-toggle">

                    <button class="btn btn-link" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Каталог продукции <span class="caret"></span>
                    </button>

                    @include('headers.common.bottom.parts.mega_menu.index')

                </li>

                <li @if(request()->url() === route('cart.show'))class="active"@endif>
                    <a href="{{ route('cart.show') }}">Корзина</a>
                </li>

                <li @if(request()->url() === route('checkout.show'))class="active"@endif>
                    <a href="{{ route('checkout.show') }}">Оформление</a>
                </li>

                <li @if(request()->url() === route('payment.show'))class="active"@endif>
                    <a href="{{ route('payment.show') }}">Оплата</a>
                </li>

            </ul>
        </div>