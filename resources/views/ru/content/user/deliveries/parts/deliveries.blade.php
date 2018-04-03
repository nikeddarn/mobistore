<div id="userDeliveries" class="table-responsive">
    <table class="table">
        <thead>
        <tr class="text-center">
            <td>Номер заказа</td>
            <td>Тип доставки</td>
            <td>Адрес доставки</td>
            <td>Дата доставки</td>
            <td>Статус</td>
            <td></td>
        </tr>
        </thead>
        <tbody>

        @foreach($userDeliveries as $delivery)
            <tr class="text-center">
                <td>
                    <span class="badge">{{ $delivery->invoices_id }}</span>
                </td>
                <td>{{ $delivery->deliveryType->title }}</td>
                <td class="user-invoice-address">{{ $delivery->userDelivery->address }}</td>
                @if($delivery->userDelivery->planned_arrival)
                    <td>{{ $delivery->userDelivery->planned_arrival->format('d-m-Y') }}</td>
                @else
                    <td>Определяется</td>
                @endif
                <td>
                    <span class="badge badge-{{$delivery->deliveryStatus->badge_class}} badge-not-rounded">{{ $delivery->deliveryStatus->title }}</span>
                </td>
                <td>
                    <button class="text-gray btn-link show-user-invoice-content-toggle" data-toggle="collapse"
                            data-target="#delivery-{{ $delivery->invoices_id }}">
                        <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                    </button>
                </td>
            </tr>
            <tr class="user-invoice-content">
                <td></td>
                <td colspan="4">
                    <div id="delivery-{{ $delivery->invoices_id }}" class="collapse">
                        @include('content.user.deliveries.parts.invoice_products')

                    </div>
                </td>
                <td></td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>