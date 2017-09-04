@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @yield('breadcrumbs')

    <div class="container">

        <div class="row">

            <!-- User Sidebar -->
            <div class="col-sm-4 col-md-3">

                <div class="m-t-4">
                    @include('headers.user.index')
                    @include('menu.user_control_menu')
                </div>


                <div class="m-t-4">
                    @include('menu.user_profile_menu')
                </div>

            </div>
            <!-- End User Sidebar -->

            <!-- User Content -->
            <div class="col-sm-8 col-md-9">

                <div class="col-sm-10 col-sm-offset-1">
                    @yield('user_content')
                </div>

            </div>
            <!-- End User Content -->


        </div>

    </div>

@endsection