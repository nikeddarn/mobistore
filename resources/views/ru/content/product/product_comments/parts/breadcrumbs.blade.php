<div class="breadcrumb-wrapper">
    <div class="container">
        <ol class="breadcrumb">

            <li><a href="/">{{ config('app.name') }}</a>

            @foreach($breadcrumbs as $breadcrumb)
                <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
            @endforeach

            <li class="active">Отзывы о товаре</li>

        </ol>
    </div>
</div>