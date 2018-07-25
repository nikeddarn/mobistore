<table class="table table-responsive">
    <thead>
    <tr>
        <td colspan="3" class="text-center"><strong>Продукты в несобранных заказах</strong></td>
    </tr>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Артикул</td>
        <td class="text-center">Количество</td>
    </tr>
    </thead>
    <tbody>

    @foreach($outgoingProducts as $product)
        <tr>
            <td>{{ $product->page_title }}</td>
            <td class="text-center">{{ $product->vendor_product_id }}</td>
            <td class="text-center">{{ $product->total_quantity }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="3">
            <div class="m-t-2 text-right">
                <form method="post" action="{{ route('vendor.order.collect.all') }}">

                    {{ csrf_field() }}
                    <input type="hidden" name="vendors_id" value="{{ $vendorId }}">

                    @foreach($outgoingOrders as $outgoingOrder)
                        <input type="hidden" name="invoices_id[]" value="{{ $outgoingOrder->id }}">
                    @endforeach

                    <button type="submit" class="btn btn-primary"><i class="fa fa-long-arrow-right"></i>Все заказано
                    </button>

                </form>
            </div>
        </td>
    </tr>

    </tbody>
</table>