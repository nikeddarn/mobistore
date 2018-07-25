<div class="row">
    <div class="col-sm-8 col-sm-offset-4">
        <table class="table table-responsive">
            <thead>
            <tr>
                <td colspan="2">Остатки гарантийного товара</td>
            </tr>
            <tr>
                <td>Наименование товара</td>
                <td class="text-center">Количество</td>
            </tr>
            </thead>
            <tbody>
            @foreach($userReclamationStock as $reclamation)
                <tr>
                    <td>{{ $reclamation->product->page_title }}</td>
                    <td class="text-center">{{ $reclamation->stock_quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>