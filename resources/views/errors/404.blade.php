<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!--[if lt IE 9]>
    <script src="{{ asset('js/html5shiv.min.js')}}"></script>
    <script src="{{ asset('js/respond.min.js')}}"></script>
    <![endif]-->
    <style>
        html, body {
            position: relative;
            height: 100vh;

        }

        .error-wrap {
            position: absolute;
            width: 100%;
            height: 100%;
            text-align: center;
            min-width: 680px;
            background-color: #f6f6f6;
        }

        .error-wrap img {
            height: 100%;
            max-width: 100%;
        }

        .not-exist {
            margin-top: 20vh;
        }

        .not-exist > div {
            display: inline-block;
        }

        .not-exist > div > p {
            font-size: 2vw;
            font-weight: 600;
        }

        .not-exist > div > .back-index {
            margin-top: 20px;
        }

        .error-wrap .back-index {
            position: absolute;
            bottom: 50px;
            left: 50%;
            margin-left: -50px;
            border-radius: 10px;
            background-color: #f6f6f6;
            color: #f0ad4e;
            font-size: 17px;
            border: 1px solid #64ccfa;
            font-weight: 700;
        }

        .error-wrap .back-index:hover, .error-wrap .back-index:focus {
            border: 1px solid #64ccfa !important;
            background-color: inherit !important;
            color: #f0ad4e !important;
            font-weight: 700 !important;
        }
    </style>
</head>
<body>
@if(preg_match('/goods\/\d+/' , request()->path()))
    <div class="container text-center not-exist">
        <img src="{{ asset('images/not-exist.png') }}"/>
        <div>
            <p>商品不存在</p>
            <a href="{{ url('/') }}" class="btn btn-primary back-index">返回首页</a>
        </div>
    </div>
@else
    <div class="container error-wrap">
        <img src="{{ asset('images/404.gif') }}">
        <a href="{{ url('/') }}" class="btn btn-primary back-index">返回首页</a>
    </div>
@endif
</body>
</html>
