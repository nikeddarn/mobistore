<div class="breadcrumb-wrapper">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="/">{{ config('app.name') }}</a>
            @foreach($breadcrumbs as $breadcrumb)
                @if($breadcrumb === end($breadcrumbs))
                    <li>{{ $breadcrumb['title'] }}</li>
                @else
                    <li><a href="/brand/{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </div>
</div>