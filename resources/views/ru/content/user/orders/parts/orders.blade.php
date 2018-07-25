<div id="userMessages" class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <td class="text-center">Дата</td>
            <td>Сообщение</td>
            <td></td>
        </tr>
        </thead>
        <tbody>

        @foreach($userMessages as $message)
            <tr>
                <td class="user-invoice-date text-center">{{ $message->created_at->format('d-m-Y') }}</td>
                <td class="text-gray @if(!$message->read_at) font-weight-bolder @endif">{{ $message->data['title'] }}</td>
                <td class="show-user-invoice-content-toggle text-center">
                    <button class="text-gray btn-link" data-message-id="{{ $message->id }}" data-toggle="collapse"
                            data-target="#message-{{ $message->id }}">
                        <span class="glyphicon glyphicon glyphicon-menu-down"></span>
                    </button>
                </td>
            </tr>
            <tr class="user-invoice-content">
                <td></td>
                <td>
                    <div id="message-{{ $message->id }}" class="collapse">
                        <p>{!! $message->data['message'] !!}</p>
                    </div>
                </td>
                <td></td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>

<div>
    <span class="col-sm-offset-1">{{ $userMessages->links() }}</span>
</div>