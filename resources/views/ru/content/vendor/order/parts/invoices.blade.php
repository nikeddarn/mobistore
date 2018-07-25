<div id="vendorOrders" class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <td colspan="4" class="text-center"><strong>Несобранные заказы</strong></td>
        </tr>
        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td></td>
        </tr>
        </thead>
        <tbody>

        @foreach($outgoingOrders as $invoice)

            <form id="collect-{{ $invoice->id }}" method="post" action="{{ route('vendor.order.collect') }}">
                {{ csrf_field() }}
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <input type="hidden" name="vendors_id" value="{{ $vendorId }}">
            </form>

            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $invoice->id }}</span></td>
                <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
                <td>{{ $invoice->invoiceType->title }}</td>
                <td>
                    @if($invoice->invoiceProduct)
                        <button class="text-gray btn-link show-user-invoice-content-toggle" data-toggle="collapse"
                                data-target="#invoice-{{ $invoice->id }}">
                            <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                        </button>
                    @endif
                </td>
            </tr>
            <tr class="user-invoice-content">
                <td colspan="4">
                    <div id="invoice-{{ $invoice->id }}" class="collapse">
                        @if($invoice->invoiceProduct)
                            @include('content.vendor.order.parts.invoice_products')
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>