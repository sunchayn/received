<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('partials.social_meta_tags')

<title>
    @hasSection('title')
        @yield('title') &ndash; {{ config('app.name', 'Received') }}
    @else
        {{ config('app.name', 'Received') }} &ndash; Public bucket for receiving files
    @endif
</title>

<link rel="icon" type="image/png" sizes="32x32" href="{{asset('static/favicon/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('static/favicon/favicon-16x16.png')}}">
<meta name="theme-color" content="#081de3">

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
