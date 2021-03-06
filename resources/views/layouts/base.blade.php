<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <main id="app" class="">
        @yield('content')
    </main>

    <script src="{{mix('js/app.js')}}"></script>

    @yield('javascript')
</body>
</html>
