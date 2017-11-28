@if(!empty($product['comments']))



    <hr>

@endif

<h4 class="m-b-2 text-gray">Добавьте ваш отзыв</h4>

<form action="#" method="post" role="form">

    {{ csrf_field() }}

    @if(!$isUserLoggedIn)
        <div class="form-group">
            <label for="product-comment-name">Имя</label>
            <input id="product-comment-name" type="text" class="form-control" placeholder="Имя" name="name">
        </div>

        <div class="form-group">
            <label for="product-comment-email">Email</label>
            <input id="product-comment-email" type="text" class="form-control" placeholder="Email" name="email">
        </div>
    @endif

    <div class="form-group">
        <label>Рейтинг</label>
        <div class="clearfix"></div>
        <input type="hidden" class="rating" name="rating" data-filled="glyphicon glyphicon-star" data-empty="glyphicon glyphicon-star-empty">
    </div>

    <div class="form-group">
        <label for="comment">Ваш комментарий</label>
        <textarea id="comment" class="form-control" rows="5" placeholder="Ваш комментарий" name="comment"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Отправить отзыв</button>

</form>