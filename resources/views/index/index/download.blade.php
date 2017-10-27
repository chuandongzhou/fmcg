@extends('index.master')

@section('subtitle', '首页')

@include('includes.jquery-lazeload')

@section('container')
    @parent
    <div class="container app-download">
        <div class="row download-role-item">
            <div class="col-xs-6 down-panel text-center">
                <h1><span class="icon-role-down dbd-buy"></span>订百达买家</h1>
                <h2 class="title">快速消费品综合服务平台</h2>
                <div class="qr-code dbd-buy-code"></div>
                <div class="code-prompt">扫描二维码即可下载订百达买家版APP</div>
            </div>
            <div class="col-xs-6">
                <img src="{{ asset('images/buy-detail-img.jpg') }}">
            </div>
        </div>
        <div class="row download-role-item">
            <div class="col-xs-6">
                <img src="{{ asset('images/sell-detail-img.png') }}">
            </div>
            <div class="col-xs-6 down-panel text-center">
                <h1><span class="icon-role-down dbd"></span>订百达卖家</h1>
                <h2 class="title">移动管理数据 , 简单易操作</h2>
                <div class="qr-code dbd-qr-code"></div>
                <div class="code-prompt">扫描二维码即可下载订百达卖家版APP</div>
            </div>
        </div>
        <div class="row download-role-item">
            <div class="col-xs-6 down-panel text-center">
                <h1><span class="icon-role-down helper"></span>司机助手</h1>
                <h2 class="title">订单实时送达、方便快捷</h2>
                <div class="qr-code helper-qr-code"></div>
                <div class="code-prompt">扫描二维码即可下载司机助手APP</div>
            </div>
            <div class="col-xs-6">
                <img src="{{ asset('images/dirver-detail-img.png') }}">
            </div>
        </div>
        <div class="row download-role-item">
            <div class="col-xs-6">
                <img src="{{ asset('images/sales-detail-img.png') }}">
            </div>
            <div class="col-xs-6 down-panel text-center">
                <h1><span class="icon-role-down field"></span>订百达外勤</h1>
                <h2 class="title">维护开发客户, 轻松提升业绩</h2>
                <div class="qr-code field-qr-code"></div>
                <div class="code-prompt">扫描二维码即可下载订百达外勤APP</div>
            </div>
        </div>
    </div>
@stop