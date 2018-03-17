<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

@yield('description')
@yield('meta_data')
@yield('styles')

<meta name="author" content="{{ trans('meta.author') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

@yield('title')

<!-- Bootstrap Core CSS -->
<link href="{{ elixir('css/app.css') }}" rel="stylesheet">

<!-- Custom CSS -->
<link href="{{ elixir('css/mobistore.css') }}" rel="stylesheet">

<!-- Custom Fonts -->
<link href="{{ elixir('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

<!-- Bootstrap-select -->
<link href="{{ elixir('css/bootstrap-select.css') }}" rel="stylesheet" type="text/css">


<!-- Scripts -->
<script type="text/javascript" src="{{ elixir('/js/app.js') }}"></script>

{{--<script type="text/javascript" src="{{ elixir('/js/bootstrap-select.js') }}"></script>--}}

{{--<script type="text/javascript" src="{{ elixir('/js/isotope.js') }}"></script>--}}

<script type="text/javascript" src="{{ elixir('/js/mobistore.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->