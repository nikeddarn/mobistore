@foreach($parentCategoriesFilters as $filter)

    <div class="filter-sidebar">

        <!-- Title -->
        <div class="col-lg-12">
            <div class="underlined-title">
                <h4 class="page-header text-gray">Категория</h4>
            </div>
        </div>

        <div class="col-lg-12">
            @foreach($filter->siblings as $sibling)
                <a href="{{ $sibling->filterUrl }}" class="filter-sidebar-checkbox">
                    <div class="checkbox">
                        <label @if($sibling->selected) class="filter-sidebar-checkbox-checked" @endif> {{ $sibling->title }}</label>
                    </div>
                </a>
            @endforeach
        </div>

    </div>

@endforeach