@extends('index.master')

@section('subtitle', '店家详情')

@section('container')
    <div class="container wholesalers-index goods-detail">
        <div class="row margin-clear">
            <div class="col-sm-8 left-store-logo store-info-logo">
                <div id="myCarousel" class="carousel slide banner-slide">
                    <ol class="carousel-indicators">
                        @foreach($shop->images as $key=>$image)
                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}">
                        @endforeach
                    </ol>
                    <div class="carousel-inner banner">
                        @foreach($shop->images as $key=>$image)
                            <div class="item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ $image->url }}" alt="{{ $image->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-4 store">
                <div class="store-panel">
                    <img class="avatar" src="{{ $shop->logo_url }}">
                    <ul class="store-msg">
                        <li>店铺名称 : {{ $shop->name }}</li>
                        <li>联系人 : {{ $shop->contact_person }}</li>
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
                            <span class="panel-name">店家介绍</span>
                            <div class="content">{{ $shop->introduction }}</div>
                            {{--<div class="address-list">--}}
                                {{--@foreach ($shop->deliveryArea as $area)--}}
                                    {{--<span>{{ $area->address_name }}</span>--}}
                                {{--@endforeach--}}
                            {{--</div>--}}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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
                <div class="item">
                    <h5 class="title-name">商品配送区域大概地图标识</h5>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            });
            getCoordinateMap({!! $coordinates !!});
        });
    </script>
@stop
