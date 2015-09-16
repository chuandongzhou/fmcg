@extends('index.menu-master')
@include('includes.address')
@section('right')
<div class="container my-goods index">
    <div class="row">
        <div class="col-sm-10 collect">
            <div class="row">
                <div class="col-sm-12 control-panel">
                    <form action="{{ url('like/shops') }}" method="get">
                        <label>配送区域</label>
                        <select class="control address-province" name="address['province_id']">
                            <option value="0">省</option>
                            <option>四川省</option>
                        </select>
                        <select class="control address-city" name="address['city_id']">
                            <option value="0">市</option>
                            <option>成都市</option>
                        </select>
                        <select class="control address-district" name="address['county_id']">
                            <option value="0">县/区</option>
                            <option>武侯区</option>
                        </select>
                        <input type="text" placeholder="经销商名称" class="control" name="user_name">
                        <button class=" btn btn-cancel search">搜索</button>
                    </form>
                </div>
            </div>
            @if(isset($res['like_shops']))
            <div class="row list-penal">
                @foreach($res['like_shops'] as $shop)
                <div class="col-sm-3 commodity">
                    <div class="img-wrap">
                        <img class="commodity-img" src="{{ $shop['image_url'] }}">
                        {{--<span class="prompt"></span>--}}
                    </div>
                    <p class="commodity-name">{{ $shop['name'] }}</p>
                    <p class="sell-panel">
                        <span class="money">最低配送额:￥{{ $shop['min_money'] }}</span>
                        <span class="sales pull-right">订单量 : {{ $shop['orders'] }}</span>
                    </p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@stop
