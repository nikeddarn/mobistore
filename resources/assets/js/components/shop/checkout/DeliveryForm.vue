<template>
    <form id="checkout_confirm_form" class="m-t-2 m-b-2" method="post" action="/checkout/confirm">


        <div class="row">

            <div class="form-group col-sm-6">
                <label for="firstNameInput">Имя</label>
                <input type="text" class="form-control" id="firstNameInput" placeholder="Ваше имя" name="name" maxlength="64" required>
                <span class="help-block">
                    <strong></strong>
                </span>
            </div>

            <div class="form-group col-sm-6">
                <label for="phoneInput">Телефон</label>
                <input type="text" class="form-control" id="phoneInput" placeholder="Номер телефона" name="phone" maxlength="32" required>
                <span class="help-block">
                    <strong></strong>
                </span>
            </div>

            <div class="form-group col-sm-6">
                <label for="deliveryType">Тип доставки</label>
                <select id="deliveryType" class="form-control selectpicker" name="delivery_type" data-style="btn-default">
                    <option>fms ffgh</option>
                    <option>fms ffgh ff f</option>
                </select>
            </div>

            <div class="form-group col-sm-6">
                <label for="postServices">Почтовая служба</label>
                <select id="postServices" class="form-control selectpicker" name="post_service" data-style="btn-default">
                    <option>fms ffgh</option>
                    <option>fms ffgh ff f</option>
                </select>
            </div>

            <div class="form-group col-sm-12">
                <label for="addressInput">Адрес доставки</label>
                <textarea class="form-control" rows="3" id="addressInput" name="address" maxlength="256" required></textarea>
                <span class="help-block">
                    <strong></strong>
                </span>
            </div>

            <div class="form-group col-sm-12">
                <label for="notesInput">Ваше сообщение</label>
                <textarea class="form-control" rows="3" id="notesInput" name="message" maxlength="256"></textarea>
                <span class="help-block">
                    <strong></strong>
                </span>
            </div>

        </div>

    </form>
</template>

<script>
    export default {
        name: "delivery-form"
    }
</script>

<style scoped>

</style>