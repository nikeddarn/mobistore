<table class="table table-responsive">
    <thead>
    <tr>
        <td>Наименование товара</td>
        <td class="text-center">Серийный номер</td>
        <td class="text-center">Статус</td>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->invoiceReclamation as $invoiceReclamation)
        <tr>
            <td>{{ $invoiceReclamation->product->page_title }}</td>
            <td class="text-center">{{ $invoiceReclamation->serial_number }}</td>
            <td class="text-center">
                @if($invoiceReclamation->accepted === 1)
                    <span class="badge badge-not-rounded badge-success">Принят</span>
                @elseif($invoiceReclamation->accepted === 0)
                    <span class="badge badge-not-rounded badge-error">Не принят</span>
                @else
                    <span class="badge badge-not-rounded badge-info">Обрабатывается</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>