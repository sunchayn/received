<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    @hasSection('title')
    @yield('title') &ndash; {{ config('app.name', 'Received') }}
    @else
        {{ config('app.name', 'Received') }}
    @endif
</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
