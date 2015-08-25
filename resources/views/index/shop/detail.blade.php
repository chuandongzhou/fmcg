@extends('index.master')

@section('subtitle', '首页')

@section('container')
    <div class="container index">
        <div class="row">
            <div class="col-sm-8 left-store-logo">
                <img class="store-logo" src="http://placehold.it/1000x400">
            </div>
            <div class="col-sm-4 store" >
                <div class="store-panel" >
                    <img class="avatar" src="{{ $shop->logo_url }}">
                    <ul class="store-msg">
                        <li>店家姓名:{{ $shop->user->user_name }}</li>
                        <li>联系人:{{ $shop->contact_person }}</li>
                        <li>最低配送额:￥{{ $shop->min_money }}</li>
                    </ul>
                </div>
                <div class="address-panel">
                    <ul>
                        <i class="icon icon-tel"></i>
                        <li class="address-panel-item">
                            <span class="panel-name">联系方式</span>
                            <span>{{ $shop->contact_info }}</span>
                        </li>
                    </ul>
                    <ul>
                        <i class="icon icon-seller"></i>
                        <li class="address-panel-item">
                            <span class="panel-name">店家地址</span>
                            <span>{{ $shop->address }}</span>
                        </li>
                    </ul>
                    <ul>
                        <i class="icon icon-address"></i>
                        <li class="address-panel-item">
                            <span class="panel-name">商品配送区域</span>
                            <span>xxx省xxx市xxx区xx街道</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row nav-wrap">
            <div class="col-sm-12 ">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbar1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a  href="#">全部</a></li>
                        <li><a href="#">热销</a></li>
                        <li><a href="#">最新</a></li>
                        <li><a href="#">促销</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            <div class="col-sm-3 commodity">
                <img class="commodity-img" src="http://placehold.it/200">
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <img class="commodity-img" src="http://placehold.it/200">
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <img class="commodity-img" src="http://placehold.it/200">
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <img class="commodity-img" src="http://placehold.it/200">
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-xs-12 text-right">
                <ul class="pagination">
                    <li class="disabled">
                        <span>«</span>
                    </li>
                    <li class="active">
                        <span>1</span>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li class="disabled">
                        <span>...</span>
                    </li>
                    <li>
                        <a href="#" rel="next">»</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop