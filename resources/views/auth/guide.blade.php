@extends('master')

@section('title' , '导航 | 订百达')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <div class="guide-content">
        <div class="container guide-container">
            <div class="row content-panel">
                <div class="col-sm-8 col-sm-offset-2 col-lg-8 col-lg-offset-2">
                    <a class="tabs-item" href="{{ url('auth/login?type=supplier') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-1.png') }}">
                        </div>
                        <span class="item-name">供应商平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=wholesaler') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-2.png') }}">
                        </div>
                        <span class="item-name">批发平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-3.png') }}">
                        </div>
                        <span class="item-name ">终端平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-4.png') }}">
                        </div>
                        <span class="item-name">零售商城</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    <footer class="panel-footer login-footer guide-footer footer">
        <div class="container text-center text-muted" >
            <div class="text-right qr-code">
                <img src="{{ asset('images/qr-code.png') }}">
                <p class="text-center">APP下载</p>
            </div>
            <div class="txt-content">
                <p class="text-left sign">Copyright2015成都订百达科技有限公司  蜀ICP备15031748号-1</p>
                <p>成都市高新区天府大道中段1388号美年广场A座1248号&nbsp;&nbsp;13829262065(霍女士)</p>
            </div>
        </div>
    </footer>
    @parent
@stop