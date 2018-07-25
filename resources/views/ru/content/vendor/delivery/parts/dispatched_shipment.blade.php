<div id="vendorShipments" class="m-b-4">
    <table class="table table-responsive">
        <thead>
        <tr>
            <td colspan="4">
                <strong>Отправленная отгрузка:&nbsp;</strong>
                <span>{{ $shipment->vendorShipment->vendorCourier->name }},&nbsp;{{ $shipment->planned_departure->format('d-m-Y') }}.&nbsp;</span>
                <span>Сумма:&nbsp;<strong>${{ $shipment->invoice->sum('invoice_sum') }}</strong></span>
            </td>
        </tr>
        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td>Сумма</td>
        </tr>
        </thead>
        <tbody>

        @foreach($shipment->invoice as $invoice)

            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $invoice->id }}</span></td>
                <td>{{ $invoice->created_at }}</td>
                <td>{{ $invoice->invoiceType->title }}</td>
                <td>{{ $invoice->invoice_sum }}</td>
            </tr>

        @endforeach

        <tr>
            <td colspan="4">
                <div class="row m-t-2">

                    <div class="col-xs-6">
                        <form method="post" action="{{ route('vendor.shipment.remove') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="shipments_id" value="{{ $shipment->id }}">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-long-arrow-right"></i>Удалить
                            </button>
                        </form>
                    </div>

                </div>
            </td>
        </tr>

        </tbody>
    </table>
</div>