<div id="store-navbar" class="navbar-middle">
    <nav class="navbar navbar-default yamm" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed pull-left" data-toggle="collapse"
                        data-target="#navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="cart.html" class="btn btn-default btn-cart-xs visible-xs pull-right">
                    <i class="fa fa-shopping-cart"></i> Cart : 4 items
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    @if(Auth::check())
                        <li class="{{ Request::path() === '/' || strpos(Request::path(), 'user') !==  false ? 'active' : '' }}"><a href="/">Личный кабинет</a></li>
                    @else
                        <li class="{{ Request::path() ==  '/' ? 'active' : ''  }}"><a href="/">Главная</a></li>
                    @endif
                        <li class="dropdown yamm-fullwidth {{ substr(Request::path(), 0, 8) === "products" ? 'active' : ''  }}">
                            <a href="/products" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                               aria-expanded="false">Каталог продукции<span class="caret"></span></a>
                            @include('menu.mega_menu.index')
                        </li>
                    <li class="{{ Request::path() ==  'cart' ? 'active' : ''  }}"><a href="/cart">Корзина</a></li>
                    <li class="{{ Request::path() ==  '/checkout' ? 'active' : ''  }}"><a href="/checkout">Оформление</a>
                    <li class="{{ Request::path() ==  '/delivery' ? 'active' : ''  }}"><a href="/delivery">Доставка</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-feature visible-lg">
                    <li><a><i class="fa fa-truck"></i> Free Shipping</a></li>
                    <li><a><i class="fa fa-money"></i> Cash on Delivery</a></li>
                    <li><a><i class="fa fa-lock"></i> Secure Payment</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
