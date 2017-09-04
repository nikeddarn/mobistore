@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <form method="post" action="/setup/confirm">
                    {{ csrf_field() }}
                    <h4 class="alert alert-danger">{{ $message }}</h4>
                    <div class="form-group">
                        <label for="setup_confirmation">Confirm</label>
                        <input style="margin-left: 3rem;" id="setup_confirmation" type="checkbox" name="setup_confirmation">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Fill Database</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection