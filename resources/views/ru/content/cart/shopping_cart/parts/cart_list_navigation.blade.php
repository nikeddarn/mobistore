<nav aria-label="Shopping Cart Next Navigation">
    <ul class="pager">

        @if(isset($productsData['back_shopping']))
            <li class="previous"><a href="{{ $productsData['back_shopping'] }}"><span aria-hidden="true">&larr;</span>
                    Продолжить покупки</a></li>
        @endif

        @if(!empty($productsData['products']))
            <li class="next"><a href="{{ $productsData['checkout_form'] }}">Оформить заказ <span aria-hidden="true">&rarr;</span></a>
            </li>
        @endif

    </ul>
</nav>
