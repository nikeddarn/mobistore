<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Подкатегория</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($childrenCategoriesFilter as $category)
            <a href="{{ $category->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label> {{ $category->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>