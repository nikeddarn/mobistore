@extends('layouts.admin')

@section('content')

    <div class="container">

        <div class="row">
            <div class="col-xs-12">
                @include('content.storage.list.parts.list')
            </div>
        </div>

    </div>

@endsection