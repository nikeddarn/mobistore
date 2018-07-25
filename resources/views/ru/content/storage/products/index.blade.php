@extends('layouts/admin')

@section('content')

    {{-- header--}}
    <div class="row">

        {{-- title --}}
        <div class="col-sm-3">
            @include('content.storage.products.parts.title')
        </div>

        {{-- products count--}}
        <div class="col-sm-8">
            <div id="user-account-balance" class="m-t-3">
                @include('content.storage.products.parts.balance')
            </div>
        </div>
    </div>

    {{-- content --}}
    <div class="row m-t-3">

        {{-- menu --}}
        <div class="col-sm-3">
            @include('content.storage.products.parts.menu')
        </div>

        {{-- invoices --}}
        <div class="col-sm-8">
            @if($storageProducts->count())
                @include('content.storage.products.parts.products')
                @else
                <h4 class="text-center">Нет продуктов на складе</h4>
            @endif
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            // change open invoice arrow direction
            $('#vendorAccount').find('button').each(function () {
                $(this).click(function (event) {

                    let openItemToggle = $(event.target).closest('button');

                    $(openItemToggle).find('span').toggleClass('glyphicon-menu-down glyphicon-menu-up');
                });
            });

        });
    </script>
@endsection