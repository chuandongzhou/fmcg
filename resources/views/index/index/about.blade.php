@extends('auth.master')

@section('title' , '关于我们 | 订百达 - 订货首选')

@section('body')
    <nav class="navbar register-nav">
        <div class="container padding-clear">
            <ul class="nav-title pull-left">
                <li><a class="logo-icon" href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="logo"/></a></li>
                <li class="title">关于我们</li>
            </ul>
            <div class="pull-right right-title">
                <a href="{{ url('/') }}">订百达首页</a>
                @if(!auth()->id())
                    <a href="{{ url('auth/login') }}">登录</a>
                @endif
            </div>

        </div>
    </nav>

    <div class="container">
        <div class="row about-contents">
            <div class="col-sm-6 col-sm-offset-3 text-center">
                {{--<h3 class="about-title">关于我们</h3>--}}

                <p>成都订百达科技有限公司是一家完全自主研发产品的新型互联网企业，公司专注于互联网传统行业的电子商务平台，致力于打造最专业的批发、超市订货电子商务平台（B2B），
                    利用互联网平台的优势，实现全国厂家、供应商、批发商、超市一站式商品销售采购和运营服务，实现传统行业低成本的全产业链。
                </p>
            </div>
            <div class="col-sm-12 text-center img-bg01 ">
            </div>
            <div class="col-sm-6 col-sm-offset-3  text-center">
                <h4 class="item-title">企业目标</h4>

                <p>为传统行业厂家、供应商、批发商、超市建立一站式服务平台，为中小零售商店打造最佳供应体系、为供应商提供最广阔的销售渠道。</p>
                <h4 class="item-title">企业名称阐述</h4>

                <p>“订”代表订货、订购，“百”意为百分百、100%，“达”则为送达、达到。“订百达”代表所有商家的订货需求我们都将百分百送达。</p>
                <h4 class="item-title">企业价值观</h4>

                <p>为客户创造价值，成就客户，完善自身。</p>
                <h4 class="item-title">我们的口号</h4>

                <p>订百达，贴心服务永不堵车。</p>
            </div>
            <div class="col-sm-12 text-center license-img">
                <a href="{{ asset('images/about-02.jpg') }}" target="_blank"><img
                            src="{{ asset('images/about-02.jpg') }}"></a>
            </div>
        </div>
    </div>
@stop