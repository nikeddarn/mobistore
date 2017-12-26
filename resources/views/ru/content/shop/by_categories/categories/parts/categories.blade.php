@foreach($categories as $category)
    <div class="col-xs-6 col-sm-3 col-md-2 category-thumbnail">
        <a href="/category/{{ $category->url }}" class="text-decoration-none">
            <div class="panel panel-default">
                <div class="panel-body">
                    <img src="/images/categories/{{ $category->image  or 'default.jpg'}}">
                </div>
                <div class="panel-heading">
                    <h4 class="text-center">{{ $category->title }}</h4>
                </div>
            </div>
        </a>
    </div>
@endforeach