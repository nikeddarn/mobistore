<div id="vendorShipments" class="m-b-4">
    <table class="table table-responsive">
        <thead>
        <tr>
            <td colspan="4" class="text-center"><strong>Неотправленные заказы</strong></td>
        </tr>
        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td>Сумма</td>
        </tr>
        </thead>
        <tbody>

        @foreach($unloadedInvoices as $invoice)

            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $invoice->id }}</span></td>
                <td>{{ $invoice->created_at }}</td>
                <td>{{ $invoice->invoiceType->title }}</td>
                <td>{{ $invoice->invoice_sum }}</td>
            </tr>

        @endforeach

        <tr>
            <td colspan="4">
                <div class="m-t-2">
                    @if($availableShipments->count())
                        @include('content.vendor.delivery.parts.add_to_shipment')
                    @else
                        <div class="text-right">
                            <a class="btn btn-primary" href="{{ route('vendor.shipment', ['vendorId' => $vendorId]) }}">Создать
                                отправку</a>
                        </div>
                    @endif
                </div>
            </td>
        </tr>

        </tbody>
    </table>
</div>