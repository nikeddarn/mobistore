<form role="form" method="POST" action="{{ route('vendor.shipment.create.schedule') }}">

    {{ csrf_field() }}

    <input type="hidden" name="vendors_id" value="{{ $vendorId }}">

    <div class="col-lg-12 form-group m-b-4{{ $errors->has('courierTourId') ? ' has-error' : '' }}">
        <label for="courierTours">Выберете тур</label>
        <select id="courierTours" class="form-control selectpicker" name="courierTourId" required>

            @foreach($courierTours as $tour)
                <option value="{{ $tour->id }}">{{ $tour->planned_departure->format('d-m-Y') }}
                    &nbsp;{{ $tour->vendorCourier->name }}</option>
            @endforeach

        </select>
        @if ($errors->has('courierTourId'))
            <span class="help-block">
                    <strong>{{ $errors->first('courierTourId') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group">
        <div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-long-arrow-right"></i>Создать</button>
        </div>
    </div>

</form>