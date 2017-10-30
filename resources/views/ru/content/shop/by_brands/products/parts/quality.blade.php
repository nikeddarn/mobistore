<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Качество</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($possibleQuality as $quality)
            <a href="{{ $quality->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label> {{ $quality->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>