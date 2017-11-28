<ul class="nav nav-tabs" role="tablist">

    <li role="presentation" class="active">
        <a href="#desc" aria-controls="desc" role="tab" data-toggle="tab">Описание</a>
    </li>

    <li role="presentation">
        <a class="nav-l" href="#detail" aria-controls="detail" role="tab" data-toggle="tab">Детали</a>
    </li>

    <li role="presentation">
        <a href="#review" aria-controls="review" role="tab" data-toggle="tab">Отзывы</a>
    </li>

</ul>

<div class="tab-content">

        <div id="desc" class="tab-pane active" role="tabpanel">
            <div class="well">{!! $product['summary'] !!}</div>
        </div>

        <div id="detail" class="tab-pane" role="tabpanel">
            <div class="well">
                @include('content.product.product_details.parts.tabs.details')
            </div>
        </div>

        <div id="review" class="tab-pane" role="tabpanel">
            <div class="well">
                @include('content.product.product_details.parts.tabs.review')
            </div>
        </div>

</div>