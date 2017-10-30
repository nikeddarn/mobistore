@foreach($brands as $brand)
    <div class="col-sm-2 category-thumbnail">
        <a href="/brand/{{ $brand->url }}" class="text-decoration-none">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="/images/brands/{{ $brand->image}}" class="img-responsive">
                </div>
                <div class="panel-heading">
                    <h4 class="text-center">{{ $brand->title }}</h4>
                </div>
            </div>
        </a>
    </div>
@endforeach