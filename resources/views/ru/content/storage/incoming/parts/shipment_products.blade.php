<table class="table table-responsive">
    <thead>
    <tr>
        <td class="text-center">Артикул</td>
        <td>Наименование товара</td>
        <td class="text-center">Количество</td>
    </tr>
    </thead>
    <tbody>
    @foreach(json_decode($incomingShipmentsProducts->get($shipment->id)->products, true) as $shipmentProduct)
        <tr>
            <td class="text-center">{{ $shipmentProduct['id'] }}</td>
            <td>{{ $shipmentProduct['title'] }}</td>
            <td class="text-center">{{ $shipmentProduct['quantity'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="4" class="text-center">
            <form id="receive-{{ $invoice->id }}" method="post"
                  action="{{ route('storage.incoming.receive.shipment') }}">
                {{ csrf_field() }}
                <input type="hidden" name="invoice_id" value="{{ $shipment->id }}">
                <input type="hidden" name="storage_id" value="{{ $storageId }}">
                <button class="btn btn-primary pull-right" type="submit">Вся отгрузка принята</button>
            </form>
        </td>
    </tr>
    </tbody>
</table>