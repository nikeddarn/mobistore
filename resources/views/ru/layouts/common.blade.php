<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Head section -->
    @include('heads.common')
</head>

<body>

<div id="app">

    <!-- Top -->
    <div id="top">
        <!-- Header -->
        @include('headers.common.index')
        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Bottom -->
    <div id="bottom" class="navbar-fixed-bottom">
        <!-- Footer -->
        @include('footers.common')
    </div>

</div>

<!-- Scripts -->
@yield ('scripts')

</body>

