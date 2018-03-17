<template>
    <delivery-form></delivery-form>
    <!--@include('content.checkout.parts.checkout_form')-->

    <!--@if(!empty($productsData['order']))-->
    <!--@include('content.checkout.parts.order')-->
    <!--@endif-->

    <!--@if(!empty($productsData['pre_order']))-->
    <!--@include('content.checkout.parts.pre_order')-->
    <!--@endif-->

    <!--@include('content.checkout.parts.control_buttons')-->
</template>

<script>
    export default {
        name: "checkout"
    }
</script>

<style scoped>

</style>