<form method="post" action="{{ route('vendor.delivery.invoices.add_to_shipment') }}">

    {{ csrf_field() }}

    <input type="hidden" name="vendors_id" value="{{ $vendorId }}">

    @foreach($unloadedInvoices as $invoice)
        <input type="hidden" name="invoices_id[]" value="{{ $invoice->id }}">
    @endforeach

    <div class="row m-t-4">

        <div class="col-sm-4">
            <label class="form-control border-none" for="vendorShipments">Добавить в отправку</label>
        </div>

        <div class="col-sm-5">
            <select id="vendorShipments" class="form-control selectpicker" name="shipments_id">
                @foreach($availableShipments as $shipment)
                    <option value="{{ $shipment->id }}">{{ $shipment->planned_departure->format('d-m-Y') }},
                        &nbsp;{{ $shipment->name }},&nbsp;Сумма:&nbsp;{{$shipment->shipment_sum}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-3">
            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-long-arrow-right"></i>Добавить
            </button>
        </div>

    </div>
</form>