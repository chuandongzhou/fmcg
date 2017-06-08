@extends('mobile.shop.master')

@section('subtitle', '店铺详情')

@section('body')
    @parent
    <div class="container-fluid  m185 p65">
        <div class="row shop-code">
            <div class="col-xs-12">
                <div class="code">
                    <img src="{{ (new \App\Services\ShopService)->qrcode($shop->id, 150, true)  }}">
                </div>
                <div class="txt">
                    扫描上面的二维码图案，进入商铺
                </div>
            </div>
            <div class="col-xs-12 contact-info">
                <div class="item">
                    <span class="prompt pull-left">联系人</span>
                    <span class="pull-right">{{ $shop->contact_person }}</span>
                </div>
                <div class="item">
                    <span class="prompt pull-left">联系方式</span>
                    <a class="pull-right phone-num" href="tel:{{ $shop->contact_info }}">{{ $shop->contact_info }} <i class="iconfont icon-dianhua1"></i></a>
                </div>
                <div class="item">
                    <span class="prompt">营业地址</span>
                    <div class="address-item">{{ $shop->address }}</div>
                </div>
            </div>
            <div class="col-xs-12 intro-panel ">
                <div class="prompt">商家介绍</div>
                <div class="content">
                    {{ $shop->introduction }}
                </div>
            </div>
        </div>
    </div>
@stop
