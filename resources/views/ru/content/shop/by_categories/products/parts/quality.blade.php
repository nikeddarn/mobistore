<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Качество</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($filters['quality'] as $quality)
            <a href="{{ $quality->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label @if($quality->selected) class="filter-sidebar-checkbox-checked" @endif> {{ $quality->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>