<form role="form" method="POST" action="{{ route('vendor.courier.create_tour') }}">

    {{ csrf_field() }}

    <input type="hidden" name="vendors_id" value="{{ $vendorId }}">

    <div class="col-lg-12 form-group m-b-4{{ $errors->has('vendor_couriers_id') ? ' has-error' : '' }}">
        <label for="vendorCouriers">Курьер</label>
        <select id="vendorCouriers" class="form-control selectpicker" name="vendor_couriers_id">

            @foreach($vendorCouriers as $courier)
                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
            @endforeach

        </select>
        @if ($errors->has('vendor_couriers_id'))
            <span class="help-block">
                    <strong>{{ $errors->first('vendor_couriers_id') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group{{ $errors->has('planned_departure') ? ' has-error' : '' }}">
        <label for="departureDate">Дата отправления</label>
        <div class="input-group date">
            <input id='departureDate' type='text' class="form-control" name="planned_departure">
            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
        </div>
        @if ($errors->has('planned_departure'))
            <span class="help-block">
                    <strong>{{ $errors->first('planned_departure') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group{{ $errors->has('planned_arrival') ? ' has-error' : '' }}">
        <label for="arrivalDate">Дата прибытия</label>
        <div class="input-group date">
            <input id='departureDate' type='text' class="form-control" name="planned_arrival">
            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
        </div>
        @if ($errors->has('planned_arrival'))
            <span class="help-block">
                    <strong>{{ $errors->first('planned_arrival') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group">
        <div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-long-arrow-right"></i>Создать</button>
        </div>
    </div>

</form>