<table class="table table-responsive">
    <thead>
    <tr>
        <td>Дата</td>
        <td class="text-center">Курьер</td>
        <td class="text-center">Телефон1</td>
        <td class="text-center">Телефон2</td>
    </tr>
    </thead>
    <tbody>
    @foreach($notDispatchedVendorShipments as $shipment)
        <tr>
            <td>{{ $shipment->planned_departure->format('d-m-Y') }}</td>
            <td class="text-center">{{ $shipment->vendorShipment->vendorCourier->name }}</td>
            <td class="text-center">{{ $shipment->vendorShipment->vendorCourier->phone1 }}</td>
            <td class="text-center">{{ $shipment->vendorShipment->vendorCourier->phone2 }}</td>
        </tr>
    @endforeach
    </tbody>
</table>