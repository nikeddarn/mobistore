<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Head section -->
    @include('heads.common')
</head>

<body>

<div id="app">

    <!-- Header -->
    @include('headers.common.index')

    <!-- Page Content -->
    @yield('content')

    <!-- Footer -->
    @include('footers.common')

</div>

<!-- Scripts -->
@yield ('scripts')

</body>

