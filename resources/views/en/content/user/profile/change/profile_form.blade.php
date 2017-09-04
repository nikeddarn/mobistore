<!-- Profile Data Edit Form -->
<form id="change-profile-form" role="form" method="POST" action="/user/profile" enctype="multipart/form-data">

    {{ csrf_field() }}

    <div class="overflow-hidden">

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Ваше имя</label>
            <div>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $name) }}" required
                       autofocus>
                @if ($errors->has('name'))
                    <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('image') ? ' has-error' : '' }}">
            <label for="image">Аватар пользователя</label>
            <div>
                <input id="image" type="file" class="form-control" name="image">
                @if ($errors->has('image'))
                    <span class="help-block">
                    <strong>{{ $errors->first('image') }}</strong>
                </span>
                @endif
            </div>
        </div>

    </div>

    <div class="overflow-hidden">

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">E-Mail Адрес</label>
            <div>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $email) }}" required
                       autofocus>
                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label for="phone">Номер Телефона</label>
            <div>
                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone', $phone) }}">
                @if ($errors->has('phone'))
                    <span class="help-block">
                    <strong>{{ $errors->first('phone') }}</strong>
                </span>
                @endif
            </div>
        </div>


    </div>

    <div class="overflow-hidden">

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('city') ? ' has-error' : '' }}">
            <label for="city">Город</label>
            <div>
                <input id="city" type="text" class="form-control" name="city" value="{{ old('city', $city) }}">
                @if ($errors->has('city'))
                    <span class="help-block">
                    <strong>{{ $errors->first('city') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="col-sm-12 col-md-6 form-group{{ $errors->has('site') ? ' has-error' : '' }}">
            <label for="site">Сайт</label>
            <div>
                <input id="site" type="text" class="form-control" name="site" value="{{ old('site', $site) }}">
                @if ($errors->has('site'))
                    <span class="help-block">
                    <strong>{{ $errors->first('site') }}</strong>
                </span>
                @endif
            </div>
        </div>

    </div>

    <div class="col-lg-12 form-group">
        <div class="pull-right">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-long-arrow-right"></i>
                Сохранить изменения
            </button>
        </div>
    </div>

</form>
<!-- End Profile Data Edit Form -->
