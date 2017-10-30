<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Цвет</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($possibleColors as $color)
            <a href="{{ $color->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label> {{ $color->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>