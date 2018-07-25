@extends('layouts/user')

@section('user_content')

    @include('content.user.payments.parts.title')

    <div class="m-t-3">
        @if($userPayments->count())
            @include('content.user.payments.parts.payments')
        @else
            <p class="text-indent">Нет платежей</p>
        @endif
    </div>

@endsection

@section('breadcrumbs')
    @include('content.user.payments.parts.breadcrumbs')
@endsection

@section('user_menu')
    @include('menu.user_sidebar_menu', ['activeMenuItem' => 'user_payments'])
@endsection

@section('meta_data')

    <title>{{ $commonMetaData['title'] }}</title>

    @if(isset($commonMetaData['description']))
        <meta name="description" content="{{ $commonMetaData['description'] }}">
    @endif

    @if(isset($commonMetaData['keywords']))
        <meta name="keywords" content="{{ $commonMetaData['keywords'] }}">
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            // mark message as 'read'
            $('#userMessages').find('button').each(function () {
                $(this).click(function (event) {

                    let openItemToggle = $(event.target).closest('button');

                    // change open message arrow direction
                    $(openItemToggle).find('span').toggleClass('glyphicon-menu-down glyphicon-menu-up');

                    let messageTitle = $(openItemToggle).closest('tr').children()[1];

                    // message already read
                    if(!$(messageTitle).hasClass('font-weight-bolder')){
                        return;
                    }

                    // mark message as 'read' on server
                    $.ajax({
                        url: '/user/messages/mark/' + $(openItemToggle).data('message-id'),
                        success: function () {
                            // set font weight normal
                            $(messageTitle).removeClass('font-weight-bolder');

                            // decrease user messages count on top panel
                            let userMessagesCountPointer = $('#header-top-user-messages-count');
                            let userMessagesCount = Math.max(0, parseInt($(userMessagesCountPointer).text()) - 1);
                            if(userMessagesCount){
                                // set new message count value
                                $(userMessagesCountPointer).text(userMessagesCount);
                            }else{
                                // remove message count pointer
                                $(userMessagesCountPointer).remove();
                            }

                            // decrease user messages count on user menu
                            let userMenuMessagesCountPointer = $('#user-menu-messages-count-pointer');
                            let userMenuMessagesCount = Math.max(0, parseInt($(userMenuMessagesCountPointer).text()) - 1);
                            if(userMenuMessagesCount){
                                // set new message count value
                                $(userMenuMessagesCountPointer).text(userMenuMessagesCount);
                            }else{
                                // remove message count pointer
                                $(userMenuMessagesCountPointer).remove();
                            }


                        },
                    });
                });
            });

        });
    </script>
@endsection