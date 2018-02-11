<div class="grid">
    @foreach($categoriesList as $category)
        <div class="col-xs-12 col-sm-3 col-md-2 grid-item m-b-2">
            <a href="/category/{{ $category->url }}" class="lead">
                <h4>
                    {{ $category->title }}&nbsp;
                    <i class="small fa fa-caret-right" aria-hidden="true"></i>
                </h4>
            </a>
            <ul class="list-unstyled hidden-xs">
                @each('menu.mega_menu.parts.subcategories', $category->children, 'category')
            </ul>
        </div>
    @endforeach
</div>