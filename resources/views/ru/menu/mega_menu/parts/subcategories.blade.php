<li>
    <a href="/category/{{ $category->url }}"><h5>{{ $category->title_ru }}</h5></a>
</li>
@if($category->children)
    @each('menu.mega_menu.subcategories', $category->children, 'category')
@endif