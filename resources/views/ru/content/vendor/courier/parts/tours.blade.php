<table class="table table-responsive">
    <thead>
    <tr>
        <td>Дата</td>
        <td class="text-center">Курьер</td>
        <td class="text-center">Телефон1</td>
        <td class="text-center">Телефон2</td>
    </tr>
    </thead>
    <tbody>
    @foreach($courierTours as $courierTour)
        <tr>
            <td>{{ $courierTour->planned_departure->format('d-m-Y') }}</td>
            <td class="text-center">{{ $courierTour->vendorCourier->name }}</td>
            <td class="text-center">{{ $courierTour->vendorCourier->phone1 }}</td>
            <td class="text-center">{{ $courierTour->vendorCourier->phone2 }}</td>
        </tr>
    @endforeach
    </tbody>
</table>