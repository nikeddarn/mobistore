<h3 class="product-detail-title text-gray">{{ $product['title'] }}</h3>

<form action="#" method="post">

    {{ csrf_field() }}

    <input type="hidden" name="id" value="{{ $product['id'] }}">

    <table class="table product-detail-table">
        <tbody>
        <tr>
            <td>Код товара</td>
            <td>{{ $product['id'] }}</td>
        </tr>
        <tr>
            <td>Цена</td>
            <td>@if($product['price']) $product['price']) @else Договорная @endif</td>
        </tr>
        <tr>
            <td>Качество</td>
            <td>{{ $product['quality'] }}</td>
        </tr>
        @if(isset($product['rating']))
            <tr>
                <td>Рейтинг</td>
                <td>
                    @for($i=1; $i<=5; $i++)
                        @if($product['rating'] >= $i)
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        @else
                            <span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
                        @endif
                    @endfor
                </td>
            </tr>
        @endif
        <tr>
            <td>Доступность</td>
            <td>
                <span class="label label-{{ $product['availability']['class'] }}">{{ $product['availability']['title'] }}</span>
            </td>
        </tr>
        <tr>
            <td>Количество</td>
            <td>
                <div class="product-detail-quantity">
                    <input id="product-detail-quantity" type="text" value="1" name="quantity">
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button class="btn btn-primary m-b-2" type="submit">
                    <i class="fa fa-shopping-cart"> Добавить в корзину</i>
                </button>
                <button class="btn btn-primary m-b-2" type="submit">
                    <i class="fa fa-shopping-cart"> Добавить в избранное</i>
                </button>
            </td>
        </tr>
        </tbody>
    </table>

</form>