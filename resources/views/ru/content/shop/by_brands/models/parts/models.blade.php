@foreach($modelsBySeries as $series)
    <div class="col-lg-12 container-fluid">
        <h3 class="text-gray text-indent">{{ $series->series }}</h3>
        <div class="row row-flex">
        @foreach($series->models as $model)
            <div class="col-sm-2 category-thumbnail">
                <a href="/brand/{{ $model->url }}" class="text-decoration-none">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <img src="/images/models/{{ $model->image  or 'default.jpg'}}" class="img-responsive">
                        </div>
                        <div class="panel-heading">
                            <h4 class="text-center">{{ $model->title }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
        </div>
    </div>
@endforeach