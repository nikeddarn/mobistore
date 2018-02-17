@extends('layouts/common')

@section('content')

    <!-- Breadcrumbs -->
    @include('content.shop.recent.parts.breadcrumbs')

    <div class="container">

        <div class="row">

            {{--Recent List--}}
            <div class="col-md-9">
                @include('content.shop.recent.parts.title')

                @include('content.shop.recent.parts.recent_list')
            </div>

            <div class="col-md-3 hidden-sm hidden-xs">

            </div>

        </div>

    </div>

    <!-- Modal -->
    @include('modals.favourite')

@endsection

@section('meta_data')

    <title>{{ $commonMetaData['title'] }}</title>

    @if(isset($commonMetaData['description']))
        <meta name="description" content="{{ $commonMetaData['description'] }}">
    @endif

    @if(isset($commonMetaData['keywords']))
        <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
    @endif

    @if(!empty($specialMetaData['meta']))
        @foreach($specialMetaData['meta'] as $meta)
            <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
        @endforeach
    @endif

    @if(!empty($specialMetaData['link']))
        @foreach($specialMetaData['link'] as $link)
            <link rel="{{ $link['rel'] }}" href="{{ $link['href'] }}">
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        $(function () {

            // initialize tooltips
            let tooltips = $('[data-toggle="tooltip"]');
            tooltips.tooltip();

            // add and remove to favourite
            $('.product-favourite, .product-not-favourite').click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                let favouriteLink = $(this);
                $.ajax({
                    url: this,
                    success: function (data) {
                        if (data !== false) {
                            let dataObject = $.parseJSON(data);
                            favouriteLink.toggleClass('product-favourite product-not-favourite');
                            favouriteLink.attr('href', dataObject.hrefReplace);
                            favouriteLink.attr('title', dataObject.title);
                            tooltips.tooltip('fixTitle');
                            showModalWindow(dataObject.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 401){
                            showModalWindow($.parseJSON(xhr.responseText).error);
                        }
                    },
                    dataType: 'json'
                });

                function showModalWindow(message) {
                    let modal = $('#favourite-product-modal');
                    $(modal).find('.modal-body p').text(message);
                    modal.modal('show');
                    let modalTimeout;
                    modal.on('shown.bs.modal', function () {
                        clearTimeout(modalTimeout);
                        modalTimeout = setTimeout(function () {
                            modal.modal('hide');
                        }, 4000)
                    });
                }

            });

        })
    </script>
@endsection