<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap Core CSS -->
<link href="{{ elixir('css/app.css') }}" rel="stylesheet">

<!-- Custom CSS -->
<link href="{{ elixir('css/mobistore.css') }}" rel="stylesheet">

<!-- Admin CSS -->
<link href="{{ elixir('css/admin.css') }}" rel="stylesheet">

<!-- Custom Fonts -->
<link href="{{ elixir('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

<!-- Bootstrap-select -->
<link href="{{ elixir('css/bootstrap-select.css') }}" rel="stylesheet" type="text/css">


<!-- Scripts -->
<script type="text/javascript" src="{{ elixir('/js/app.js') }}"></script>

<script type="text/javascript" src="{{ elixir('/js/bootstrap-select.js') }}"></script>

<script type="text/javascript" src="{{ elixir('/js/admin.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>