@extends('layouts.admin')

@section('content')

    <div class="container">

        <div class="row">

            <div class="col-sm-4 col-md-3">
                @include('menu.admin.admin_control_panel')
            </div>

            <div class="col-sm-8 col-md-9">
                <div><h3 class="text-center">Категории товаров</h3></div>
            </div>

        </div>

    </div>

@endsection