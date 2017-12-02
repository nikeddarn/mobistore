<h4 class="m-b-2 text-gray">Добавьте ваш отзыв</h4>

<form action="/comment/product" method="post" role="form">

    {{ csrf_field() }}

    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

    @if(!$isUserLoggedIn)
        <div class="form-group">
            <label for="product-comment-name">Имя</label>
            <input id="product-comment-name" type="text" class="form-control" placeholder="Имя" name="name"
                   maxlength="32">
            @if ($errors->has('name'))
                <span class="help-block alert alert-danger">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    @endif

    <div class="form-group">
        <label>Рейтинг продукта</label>
        <div class="clearfix"></div>
        <div class="product-rating">
            <input type="hidden" class="rating" name="rating" data-filled="glyphicon glyphicon-star"
                   data-empty="glyphicon glyphicon-star-empty" value="0">
        </div>
    </div>

    <div class="form-group">
        <label for="comment">Ваш комментарий</label>
        <textarea id="comment" class="form-control" rows="5" placeholder="Ваш комментарий" name="comment"
                  maxlength="512"></textarea>
        @if ($errors->has('comment'))
            <span class="help-block alert alert-danger">
                <strong>{{ $errors->first('comment') }}</strong>
            </span>
        @endif
    </div>


    <button type="submit" class="btn btn-primary">Отправить отзыв</button>

</form>