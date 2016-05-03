@extends('index.menu-master')
@section('subtitle', '个人中心-店铺介绍')
@include('includes.qrcode')

@section('right')
    <div class="col-sm-12 store goods-detail  wholesalers-index personal-store">
        <div class="row">
            <div class="col-sm-10">
                <div class="store-panel">
                    <img class="avatar" src="{{ $shop->logo_url }}">
                    <ul class="store-msg">
                        <li>店家名称 : {{ $shop->name }} &nbsp;&nbsp;
                            <a href="javascript:" class="qrcode" data-target="#qrcodeModal" data-toggle="modal" data-url="{{ url('shop/' . $shop->id) }}">店铺二维码</a></li>
                        <li>联系人 : {{ $shop->contact_person }}</li>
                        <li>最低配送额 : {{ $shop->min_money }}</li>
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
                            <span class="panel-name">店家介绍</span>

                            <div class="content">
                                {{ $shop->introduction }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-2 text-center">
                <a href="{{ url('personal/shop') }}" class="btn btn-primary">编辑</a>
            </div>
        </div>
        @if($shop->user->type != cons('user.type.retailer'))
            <div class="row nav-wrap">
                <div class="col-sm-12 switching">
                    <a href="#" class="active">配送区域</a>
                </div>
                <div class="col-sm-12 address-wrap">
                    <div class="item clearfix">
                        <h5 class="title-name">商品配送区域</h5>
                        <ul class="address-list">
                            @foreach($shop->deliveryArea as $area)
                                <li>{{ $area->address_name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    {{--<div class="item">--}}
                    {{--<h5 class="title-name">商品配送区域大概地图标识</h5>--}}

                    {{--<div id="map"></div>--}}
                    {{--</div>--}}
                </div>
            </div>
        @endif
    </div>
@stop

