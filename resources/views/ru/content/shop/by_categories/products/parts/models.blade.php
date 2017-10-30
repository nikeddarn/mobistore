<div class="filter-sidebar">

    <!-- Title -->
    <div class="col-lg-12">
        <div class="underlined-title">
            <h4 class="page-header text-gray">Модель</h4>
        </div>
    </div>

    <div class="col-lg-12">
        @foreach($possibleModels as $model)
            <a href="{{ $model->filterUrl }}" class="filter-sidebar-checkbox">
                <div class="checkbox">
                    <label @if($model->selected) class="filter-sidebar-checkbox-checked" @endif> {{ $model->title }}</label>
                </div>
            </a>
        @endforeach
    </div>

</div>