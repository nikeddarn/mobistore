<li>
    <a href="/category/{{ $category->url }}">{{ $category->title_ru }}</a>
</li>
@if($category->children)
    @each('headers.common.bottom.parts.mega_menu.subcategories', $category->children, 'category')
@endif