<div id="userAccount" class="table-responsive">
    <table class="table user-layout-table">
        <thead>

        <tr>
            <td colspan="5" class="text-center">
                <span class="user-layout-table-header">Все инвойсы пользователя</span>
            </td>
        </tr>

        <tr class="table-row-separator"></tr>

        <tr class="text-center">
            <td>Инвойс</td>
            <td>Дата</td>
            <td>Тип инвойса</td>
            <td>Сумма</td>
            <td></td>
        </tr>

        </thead>
        <tbody>

        @foreach($userInvoices['invoices'] as $invoice)

            <tr @if(isset($invoice['details']))
                class="item-details-toggle text-center"
                data-invoice-id="{{ $invoice['id'] }}"
                data-toggle="collapse"
                data-target="#invoice-{{ $invoice['id'] }}"
                    @endif>

                <td><span class="badge">{{ $invoice['id'] }}</span></td>

                <td>{{ $invoice['createdAt'] }}</td>

                <td>
                    @if($invoice['direction'] === 'in')
                        <span class="glyphicon glyphicon-arrow-down text-danger" aria-hidden="true"></span>
                    @elseif($invoice['direction'] === 'out')
                        <span class="glyphicon glyphicon-arrow-up text-success" aria-hidden="true"></span>
                        @endif
                        &emsp;{{ $invoice['type'] }}
                </td>

                <td>{{ $invoice['sum'] }}</td>

                <td class="text-gray">
                    @if(isset($invoice['details']))
                        <span class="item-details-pointer glyphicon glyphicon-chevron-right pull-right"
                              aria-hidden="true"></span>
                    @endif
                </td>

            </tr>

            @if(isset($invoice['details']))

                <tr class="user-invoice-details">

                    <td colspan="5" class="text-center">

                        <div id="invoice-{{ $invoice['id'] }}" class="collapse">

                            <div class="user-invoice-details-content">

                                @if(isset($invoice['details']['products']))

                                    @include('content.user.balance.parts.products_details')

                                @elseif(isset($invoice['details']['defectProducts']))

                                    @include('content.user.balance.parts.defects_product_details')

                                @endif

                            </div>

                        </div>

                    </td>

                </tr>

            @endif

        @endforeach

        </tbody>
    </table>
</div>

@if(isset($userInvoices['links']))
    <div>
        <span class="col-sm-offset-1">{{ $userInvoices['links'] }}</span>
    </div>
@endif