@foreach($brandsList as $brand)
    <div class="col-xs-12 col-sm-3 col-md-2 m-b-2">
        <div class="col-xs-12 visible-xs">
            <a href="/brand/{{ $brand->url }}"><h4>{{ $brand->title }}</h4></a>
        </div>
        <div class="hidden-xs">
            <a href="/brand/{{ $brand->url }}" class="thumbnail">
                <img src="/images/brands/{{ $brand->image }}" class="img-responsive">
            </a>
        </div>
    </div>
@endforeach