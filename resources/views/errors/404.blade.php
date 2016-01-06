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
        html,body{
            position:relative;
            height: 100%;
            background-color: #f6f6f6;
        }
        .container{
            position:absolute;
            width: 100%;
            height: 100%;
            text-align: center;
            min-width: 680px;
        }
        img{
            height: 100%;
            max-width: 100%;
        }
        .back-index{
            position:absolute;
            bottom:50px;
            left:50%;
            margin-left: -50px;
            border-radius: 10px;
            background-color: #f6f6f6;
            color: #f0ad4e;
            font-size: 17px;
            border:1px solid #64ccfa;
            font-weight: 700;
        }
        .back-index:hover,.back-index:focus{
            border:1px solid #64ccfa !important;
            background-color: inherit !important;
            color: #f0ad4e!important;
            font-weight: 700!important;
        }
    </style>
</head>
<body >
<div class="container" >
    <img src="{{ asset('images/404.gif') }}" >
    <a href="{{ url('/') }}" class="btn btn-primary back-index">返回首页</a>
</div>

</body>
</html>
