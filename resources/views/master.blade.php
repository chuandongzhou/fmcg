<!DOCTYPE html>
<html lang="zh-cmn-Hans">
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no, email=no">
        <meta name="keywords" content="">
        <meta name="description" content="">
        @yield('meta')
        <title>@yield('title')</title>

        <link href="/favicon.ico" rel="shortcut icon">
        <link href="/favicon.ico" type="image/x-icon" rel="bookmark">
        <link href="{{ asset('css/style.css?v=1.0.0') }}" rel="stylesheet">
        <script src="{{ asset('js/js.cookie.js') }}"></script>
        @yield('css')

    <!--[if lt IE 9]>
        <script src="{{ asset('js/html5shiv.min.js') }}"></script>
        <script src="{{ asset('js/respond.min.js') }}"></script>
        <script src="{{ asset('js/selectivizr.js') }}"></script>
        <![endif]-->
        <script>
            var SITE = {
                USER: {!! $user or '{}' !!},
                ROOT: '{{ url('/') }}',
                API_ROOT: '{{ route('api.v1.root') }}'
            };
        </script>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/lib/jquery/placeholder/jquery.placeholder.min.js') }}"></script>
    </head>
    <body>

        @yield('header')
        @yield('body')
        @yield('footer')

        <script src="{{ asset('js/ie10-viewport-bug-workaround.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        <script src="{{ asset('js/common.js?v=1.0.0') }}"></script>
        @yield('js-lib')
        @yield('js')

    </body>
</html>