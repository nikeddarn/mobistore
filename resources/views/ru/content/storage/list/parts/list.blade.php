<ul class="list-unstyled">
    @foreach($storages as $storage)
        <li class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ route('storage.product', ['storageId' => $storage->id]) }}">
                        <div class="text-gray">
                            <span>{{ $storage->title }}</span>
                            <span class="pull-right">Инвойсов: {{ $storage->storageInvoice->count() }}</span>
                        </div>
                    </a>
                </div>
            </div>
        </li>
    @endforeach
</ul>
