@extends('master')

@section('js-lib')
    <script src="{{ asset('js/index.js') }}"></script>
@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <style>
        html, body {
            background-color: #f1f1f1;
        }
    </style>
@stop

@section('header')
    <nav class="navbar register-nav">
        <div class="container padding-clear">
            <ul class="nav-title pull-left">
                <li><a class="logo-icon" href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="logo"/></a></li>
                <li class="title">注册</li>
            </ul>
            <div class="pull-right right-title">
                <a href="{{ url('/') }}">订百达首页</a>
                <a href="{{ url('/about') }}">关于我们</a>
            </div>

        </div>
    </nav>

@stop
@section('footer')
    @include('includes.footer', ['class' => 'register-footer'])
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            //意见反馈
            $('.feedback-panel > a').popover({
                container: '.feedback-panel',
                placement: 'top',
                html: true,
                content: function () {
                    return $(this).parent().siblings('.content').html();
                }
            })

            //扫二维码下载app
            tooltipFunc('#qr-content-panel > a', '#qr-content-panel');
            //联系方式
            tooltipFunc('.contact-panel > a', '.contact-panel');

            //调用tooltip插件
            function tooltipFunc(item, container) {
                $(item).tooltip({
                    container: container,
                    placement: 'top',
                    html: true,
                    title: function () {
                        return $(this).parent().siblings('.content').html();
                    }
                })
            }

        });
    </script>
@stop