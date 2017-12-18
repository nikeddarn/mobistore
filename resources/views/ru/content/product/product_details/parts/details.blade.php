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
            <td class="product-price">
                @if($product['price'])
                    @if($product['priceUah'])
                        <span>{{ $product['priceUah'] }}&nbsp; грн</span>
                    @endif
                    <span class="col-xs-offset-1">${{ $product['price'] }}</span>
                @else
                    <span>Договорная</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Качество</td>
            <td>{{ $product['quality'] }}</td>
        </tr>
        @if(isset($product['rating']))
            <tr>
                <td>Рейтинг</td>
                <td>
                    <div class="product-rating">
                        @for($i=1; $i<=5; $i++)
                            @if($product['rating'] >= $i)
                                <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                            @else
                                <span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>
                            @endif
                        @endfor
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <td>Доступность</td>
            <td>

                @if($product['stockStatus'] === 1)
                    <span class="label label-success">товар на складе</span>
                @elseif($product['stockStatus'] === 0)
                    <span class="label label-info">товар под заказ</span>
                @elseif($product['stockStatus'] === null)
                    <span class="label label-warning">товара нет в наличии</span>
                @endif

                <span class="col-xs-offset-1">
                    @foreach($product['stockLocations'] as $location)
                        <span class="label label-info">{{ $location }}</span>
                    @endforeach
                </span>

            </td>
        </tr>
        @if($product['stockStatus'] !== null)
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
        @endif
        </tbody>
    </table>

</form>