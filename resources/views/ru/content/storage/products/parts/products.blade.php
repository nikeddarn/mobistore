<div id="vendorAccount" class="table-responsive">
    <table class="table">
        <thead>
        <tr class="text-center">
            <td class="text-center">Артикул</td>
            <td>Наименование товара</td>
            <td class="text-center">На складе</td>
            <td class="text-center">Зарезервировано</td>
            <td class="text-center">Свободно</td>
            <td></td>
        </tr>
        </thead>
        <tbody>

        @foreach($storageProducts as $storageProduct)
            <tr class="text-center">
                <td class="user-invoice-id"><span class="badge">{{ $storageProduct->products_id }}</span></td>
                <td>{{ $storageProduct->product->page_title }}</td>
                <td>{{ $storageProduct->stock_quantity }}</td>
                <td>{{ $storageProduct->reserved_quantity }}</td>
                <td>{{ max(($storageProduct->stock_quantity - $storageProduct->reserved_quantity), 0) }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>