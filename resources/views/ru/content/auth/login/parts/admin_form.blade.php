<form id="login-form" role="form" method="POST" action="/login">

    {{ csrf_field() }}

    <div class="col-lg-12 form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <label for="email">E-Mail Адрес</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required
                   autofocus>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
    </div>

    <div class="col-lg-12 form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        <label for="password">Пароль</label>
            <input id="password" type="password" class="form-control" name="password" required>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
    </div>

    <div class="col-lg-12 form-group">
        <div>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-long-arrow-right"></i>
                Войти
            </button>

            <a class="btn btn-link" href="/forgot">
                Забыли пароль?
            </a>
        </div>
    </div>

</form>