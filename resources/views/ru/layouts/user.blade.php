@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @yield('breadcrumbs')

    <div class="container">

        <div class="row">

            <!-- User Sidebar -->
            <div id="user-sidebar" class="col-sm-4 col-md-3">

                @include('menu/user_sidebar_menu')

            </div>
            <!-- End User Sidebar -->

            <!-- User Content -->
            <div class="col-sm-8 col-md-9">

                @yield('user_content')

            </div>
            <!-- End User Content -->


        </div>

    </div>

@endsection