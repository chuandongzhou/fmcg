<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="format-detection" content="telephone=no, email=no">
    <meta name="keywords" content="">
    <meta name="description" content="">
    @yield('meta')
    <title>@yield('title')</title>

    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon">
    <link href="{{ asset('favicon.ico') }}" type="image/x-icon" rel="bookmark">
    <link href="{{ asset('css/style.css?v=1.0.0') }}" rel="stylesheet">
    <script src="{{ asset('js/js.cookie.js') }}"></script>
    @yield('css')

    <!--[if lt IE 9]>
    <script src="{{ asset('js/html5shiv.min.js') }}"></script>
    <script src="{{ asset('js/respond.min.js') }}"></script>
    <script src="{{ asset('js/selectivizr.js') }}"></script>
    <![endif]-->
    <!-- 对于IE 10 以下版本placeholder的兼容性调整 -->
    <!--[if lt IE 10]>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[placeholder]').removeAttr("placeholder");
        })
    </script>
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
<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">

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