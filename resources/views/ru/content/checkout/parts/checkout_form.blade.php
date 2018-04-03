<form id="checkout_confirm_form" class="m-t-2 m-b-2" method="post" action="{{ route('checkout.confirm') }}">

    {{ csrf_field() }}

    <div class="row">

        <div class="form-group col-sm-6">
            <label for="firstNameInput">Имя</label>
            <input type="text" class="form-control" id="firstNameInput" placeholder="Ваше имя" name="name" maxlength="64" @if(isset($deliveryData['name']))value="{{ $deliveryData['name'] }}" @endif required>
            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group col-sm-6">
            <label for="phoneInput">Телефон</label>
            <input type="text" class="form-control" id="phoneInput" placeholder="Номер телефона" name="phone" maxlength="32" @if(isset($deliveryData['phone']))value="{{ $deliveryData['phone'] }}" @endif required>
            @if ($errors->has('phone'))
                <span class="help-block">
                    <strong>{{ $errors->first('phone') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group col-sm-6">
            <label for="deliveryType">Тип доставки</label>
            <select id="deliveryType" class="form-control selectpicker" name="delivery_type" data-style="btn-default">
                @foreach($deliveryData['types'] as $deliveryTypeId => $deliveryType)
                <option value="{{ $deliveryTypeId }}">{{ $deliveryType }}</option>
                @endforeach
            </select>
        </div>

        <div id="cityDeliverySelect" class="form-group col-sm-6">
            <label for="postServices">Город курьерской доставки</label>
            <select id="postServices" class="form-control selectpicker" name="courier_delivery_city" data-style="btn-default">
                @foreach($deliveryData['cities'] as $cityId => $city)
                    <option value="{{ $cityId }}">{{ $city }}</option>
                @endforeach
            </select>
        </div>

        <div id="postServiceSelect" class="form-group col-sm-6 hidden">
            <label for="postServices">Почтовая служба</label>
            <select id="postServices" class="form-control selectpicker" name="post_service" data-style="btn-default">
                @foreach($deliveryData['posts'] as $postServiceId => $postService)
                    <option value="{{ $postServiceId }}">{{ $postService }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-12">
            <label for="addressInput">Адрес доставки</label>
            <textarea class="form-control" rows="3" id="addressInput" name="address" maxlength="256" required>@if(isset($deliveryData['address'])){{ $deliveryData['address'] }} @endif</textarea>
            @if ($errors->has('address'))
                <span class="help-block">
                    <strong>{{ $errors->first('address') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group col-sm-12">
            <label for="notesInput">Ваше сообщение</label>
            <textarea class="form-control" rows="3" id="notesInput" name="message" maxlength="256"></textarea>
            @if ($errors->has('message'))
                <span class="help-block">
                    <strong>{{ $errors->first('message') }}</strong>
                </span>
            @endif
        </div>

    </div>

</form>