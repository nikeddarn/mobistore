@component('mail::message')

    <div>

        {{-- Header --}}
        <h1 style="text-align: center">{{trans('passwords.mail.header')}}</h1>

        {{-- Intro Lines --}}
        @foreach (trans('passwords.mail.lines') as $line)
            <p>{{ $line }}</p>
        @endforeach

        @component('mail::button', ['url' => $url, 'color' =>$color])
            {{ $actionText }}
        @endcomponent

    </div>

@endcomponent