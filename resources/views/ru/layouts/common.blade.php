<!DOCTYPE html>
<html lang="ru">

<head>
    <!-- Head section -->
    @include('heads/common')
</head>

<body>

<!-- Header -->
@include('headers/common/index')

<!-- Page Content -->
@yield('content')

<!-- Footer -->
@include('footers/common')

<!-- Scripts -->
@yield ('scripts')

</body>

