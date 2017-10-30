<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Бренд</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($possibleBrands as $brand)
            <a href="{{ $brand->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label @if($brand->selected) class="filter-sidebar-checkbox-checked" @endif> {{ $brand->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>