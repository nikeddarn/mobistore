<div class="row">
    <div class="col-xs-8 col-xs-offset-4 col-sm-4 col-sm-offset-8">
        <div class="panel panel-default panel-horizontal">
            <div class="panel-heading">
                <h3 class="panel-title">Всего продуктов</h3>
            </div>
            <div class="panel-body text-center">{{ $storageProducts->pluck('stock_quantity')->sum() }}</div>
        </div>
    </div>
</div>