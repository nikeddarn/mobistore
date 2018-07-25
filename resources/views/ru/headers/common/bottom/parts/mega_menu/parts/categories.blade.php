<div class="grid product-path-content">
    @if($categoriesList)
        @foreach($categoriesList as $category)
            <div class="col-xs-12 col-sm-3 grid-item m-b-2">
                <a href="/category/{{ $category->url }}" class="lead">
                    {{ $category->title }}&nbsp;
                    <i class="small fa fa-caret-right" aria-hidden="true"></i>
                </a>
                <ul class="list-unstyled hidden-xs">
                    @each('headers.common.bottom.parts.mega_menu.parts.subcategories', $category->children, 'category')
                </ul>
            </div>
        @endforeach
    @endif
</div>