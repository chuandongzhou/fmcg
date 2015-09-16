@extends('index.index-master')

@section('subtitle', '首页')

@section('container')
    <div class="container dealer-index index search-page">
        <div class="row sort">
            <div class="col-sm-12">
                <div class="tab-title clearfix">
                    <p class="pull-left sequence">
                        <a href="{{ url('shop') }}"
                           class="{{ $sort == '' ? 'active' : '' }} control">全部</a>
                        <a href="{{ url('shop/hot') }}"
                           class="control {{ $sort == 'hot' ? 'active' : '' }}">热门</a>
                        <a href="{{ url('shop/new') }}"
                           class="control {{ $sort == 'new' ? 'active' : '' }}">最新</a>
                    </p>

                    <p class="pull-right">
                        <span>配送区域</span>
                        <select name="province_id" class="address-province"></select>
                        <select name="city_id" class="address-city"></select>
                        <select name="district_id" class="address-district"> </select>
                        <select name="street_id" class="address-street"> </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            @foreach($shops  as $item)
                <div class="col-sm-3 commodity">
                    <div class="img-wrap"><img class="commodity-img" src="{{ $item->image_url }}"></div>
                    <div class="content-panel">
                        <p class="sell-panel">
                            <span class="sales ">最底配送额</span>
                            <span class="money pull-right">￥{{ $item->min_money }}</span>
                        </p>

                        <p class="commodity-name">{{ $item->name }}</p>

                        <p class="order-count">订单量 : <span>10010</span></p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                {{--{{ $shops->render() }}--}}
            </div>
        </div>
    </div>
@stop
@section('js-lib')
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
