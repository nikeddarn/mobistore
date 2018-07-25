@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @yield('breadcrumbs')

    <div class="container">

        <div class="row">

            <!-- User Sidebar -->
            <div class="col-sm-4 col-md-3 m-t-3">

                <div id="user-logotype" class="m-b-4">

                    @if(isset($userImage))
                        <img src="{{ $userImage }}" alt="Аватар пользователя" class="hidden-xs img-responsive img-circle">
                    @else
                        <img src="/images/common/no_user_image.png" alt="Аватар пользователя"
                             class="hidden-xs img-responsive img-circle">
                    @endif

                </div>

                <nav class="user-menu">
                    @yield('user_menu')
                </nav>

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