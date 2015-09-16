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
                        <select class="control address-province" name="province_id">
                            <option value="0">省</option>
                            <option value="510000">四川省</option>
                        </select>
                        <select class="control address-city" name="city_id">
                            <option value="0">市</option>
                            <option>成都市</option>
                        </select>
                        <select class="control address-district" name="county_id">
                            <option value="0">县/区</option>
                            <option>武侯区</option>
                        </select>
                        <input type="text" placeholder="经销商名称" class="control" name="user_name">
                        <button class=" btn btn-cancel search">搜索</button>
                    </form>
                </div>
            </div>
            <div class="row list-penal">
                @foreach($shops as $shop)
                <div class="col-sm-3 commodity">
                    <div class="img-wrap">
                        <img class="commodity-img" src="{{ $shop['image_url'] }}">
                    </div>
                    <p class="commodity-name">{{ $shop['name'] }}</p>
                    <p class="sell-panel">
                        <span class="money">最低配送额:￥{{ $shop['min_money'] }}</span>
                        <span class="sales pull-right">订单量 : {{ $shop['orders'] }}</span>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop
