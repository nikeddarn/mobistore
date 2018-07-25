<form role="form" method="POST" action="{{ route('vendor.courier.create') }}">

    {{ csrf_field() }}

    <input type="hidden" name="vendors_id" value="{{ $vendorId }}">

    <div class="col-lg-12 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        <label for="courierName">Имя курьера</label>
        <input id='courierName' type='text' class="form-control" name="name"  value="{{ old('name') }}">
        @if ($errors->has('name'))
            <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group{{ $errors->has('phone1') ? ' has-error' : '' }}">
        <label for="phone1">Телефон1</label>
        <input id='phone1' type='text' class="form-control" name="phone1" value="{{ old('phone1') }}">
        @if ($errors->has('phone1'))
            <span class="help-block">
                    <strong>{{ $errors->first('phone1') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group{{ $errors->has('phone2') ? ' has-error' : '' }}">
        <label for="phone2">Телефон2</label>
        <input id='phone2' type='text' class="form-control" name="phone2" value="{{ old('phone2') }}">
        @if ($errors->has('phone2'))
            <span class="help-block">
                    <strong>{{ $errors->first('phone2') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-lg-12 form-group">
        <div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-long-arrow-right"></i>Создать</button>
        </div>
    </div>

</form>