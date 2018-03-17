<div id="userAccount" class="table-responsive">
    <table class="table">
        <thead>
        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td class="text-center">Сумма</td>
            <td class="text-center">Статус</td>
            <td></td>
        </tr>
        </thead>
        <tbody>

        @foreach($userInvoices as $invoice)
            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $invoice->id }}</span></td>
                <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
                <td>{{ $invoice->invoiceType->title }}</td>
                <td>${{ $invoice->invoice_sum }}</td>
                <td>
                    <span class="badge badge-{{$invoice->invoiceStatus->badge_class}} badge-not-rounded">{{ $invoice->invoiceStatus->title }}</span>
                </td>
                <td>
                    @if($invoice->invoiceProduct || $invoice->invoiceReclamation)
                        <button class="text-gray btn-link show-user-invoice-content-toggle" data-toggle="collapse"
                                data-target="#invoice-{{ $invoice->id }}">
                            <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                        </button>
                    @endif
                </td>
            </tr>
            <tr class="user-invoice-content">
                <td></td>
                <td colspan="4">
                    <div id="invoice-{{ $invoice->id }}" class="collapse">
                        @if($invoice->invoiceProduct)
                            @include('content.user.account.parts.invoice_products')
                        @elseif($invoice->invoiceReclamation)
                            @include('content.user.account.parts.invoice_reclamations')
                        @endif
                    </div>
                </td>
                <td></td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>