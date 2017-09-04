@if (session('status'))
    <div class="col-sm-8">
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    </div>
@endif

<form id="forgot-form" role="form" method="POST" action="/forgot">

    {{ csrf_field() }}

    <div class="col-sm-8 form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <label for="email">E-Mail Адрес</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required
               autofocus>
        @if ($errors->has('email'))
            <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
        @endif
    </div>

    <div class="col-sm-8 form-group">
        <div>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-long-arrow-right"></i>
                Отправить запрос
            </button>
        </div>
    </div>

</form>