<div id="vendorShipments" class="m-b-4">
    <table class="table table-responsive">
        <thead>
        <tr>
            <td colspan="4">
                <h4 class="text-gray">Входящая отгрузка</h4>
            </td>
        </tr>

        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td></td>
        </tr>

        </thead>
        <tbody>

        @foreach($shipment->invoice as $invoice)

            <form id="receive-{{ $invoice->id }}" method="post"
                  action="{{ route('storage.incoming.receive.invoice') }}">
                {{ csrf_field() }}
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <input type="hidden" name="storage_id" value="{{ $storageId }}">
            </form>

            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $invoice->id }}</span></td>
                <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
                <td>{{ $invoice->invoiceType->title }}</td>
                <td>
                    @if($invoice->invoiceProduct)
                        <button class="text-gray btn-link show-user-invoice-content-toggle"
                                data-toggle="collapse"
                                data-target="#invoice-{{ $invoice->id }}">
                            <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                        </button>
                    @endif
                </td>
            </tr>

            <tr class="user-invoice-content">
                <td></td>
                <td colspan="2">
                    <div id="invoice-{{ $invoice->id }}" class="collapse">
                        @if($invoice->invoiceProduct)
                            @include('content.storage.incoming.parts.invoice_products')
                        @endif
                    </div>
                </td>
                <td></td>
            </tr>

        @endforeach

        <tr>
            <td colspan="3" class="text-right"><strong>Показать все товары отгрузки</strong></td>
            <td class="text-center">
                <button class="text-gray btn-link show-user-invoice-content-toggle"
                        data-toggle="collapse"
                        data-target="#shipment-products-{{ $shipment->id }}">
                    <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                </button>
            </td>
        </tr>

        <tr class="user-invoice-content">
            <td></td>
            <td colspan="2">
                <div id="shipment-products-{{ $shipment->id }}" class="collapse">
                    @if($invoice->invoiceProduct)
                        @include('content.storage.incoming.parts.shipment_products')
                    @endif
                </div>
            </td>
            <td></td>
        </tr>

        </tbody>
    </table>
</div>