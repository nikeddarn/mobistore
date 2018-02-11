<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Head section -->
    @include('heads.admin')
</head>

<body>

@include('headers.admin.index')

    <!-- Page Content -->
    @yield('content')


<!-- Scripts -->
@yield ('scripts')

</body>

