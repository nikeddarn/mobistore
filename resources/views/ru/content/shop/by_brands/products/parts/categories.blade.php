    <div class="filter-sidebar">

        <!-- Title -->
        <div class="col-lg-12">
            <div class="underlined-title">
                <h4 class="page-header text-gray">Категория</h4>
            </div>
        </div>

        <div class="col-lg-12">
            @foreach($categoriesFilter as $filterItem)
                <a href="{{ $filterItem->filterUrl }}" class="filter-sidebar-checkbox">
                    <div class="checkbox">
                        <label @if($filterItem->selected) class="filter-sidebar-checkbox-checked" @endif> {{ $filterItem->title }}</label>
                    </div>
                </a>
            @endforeach
        </div>

    </div>