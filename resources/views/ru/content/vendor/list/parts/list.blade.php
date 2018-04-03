<ul class="list-unstyled">
    @foreach($vendors as $vendor)
        <li class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ route('vendor.account', ['vendorId' => $vendor->id]) }}">
                        <div class="text-gray">
                            <span>{{ $vendor->title }}</span>
                            <span class="pull-right">Инвойсов: {{ $vendor->vendorInvoice->count() }}</span>
                        </div>
                    </a>
                </div>
            </div>
        </li>
    @endforeach
</ul>
