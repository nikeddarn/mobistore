@if(!empty($comments))

    @foreach($comments as $comment)

        <div class="media">
            <div class="media-left">

                @if(isset($comment['userImage']))
                    <img src="{{ $comment['userImage'] }}" class="media-object img-thumbnail" alt="фото пользователя">
                @else
                    <img src="/public/images/common/default.png" class="media-object img-thumbnail"
                         alt="нет фото пользователя">
                @endif

                @if(isset($comment['rating']))
                    <div class="m-t-1">
                        <div class="product-rating">
                            @for($i=1; $i<=5; $i++)
                                @if($comment['rating'] >= $i)
                                    <span class="glyphicon glyphicon-star"></span>
                                @else
                                    <span class="glyphicon glyphicon-star-empty"></span>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endif

            </div>
            <div class="media-body">
                <h5 class="media-heading text-gray">
                    <strong>{{ $comment['userName'] }}</strong>
                    <span class="pull-right small">{{ $comment['date'] }}</span>
                </h5>
                <div>{{$comment['comment']}}</div>
            </div>
        </div>

        <hr>

    @endforeach
@endif