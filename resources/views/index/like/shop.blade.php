@extends('index.menu-master')
@include('includes.address')
@section('right')
<div class="container my-goods index">
    <div class="row">
        <div class="col-sm-10 collect">
            <div class="row">
                <div class="col-sm-12 control-panel">
                    <label>配送区域</label>
                    <select class="control address-province" name="province_id">
                        <option value="0">省</option>
                        <option>四川省</option>
                    </select>
                    <select class="control address-city" name="city_id">
                        <option value="0">市</option>
                        <option>成都市</option>
                    </select>
                    <select class="control address-district" name="county_id">
                        <option value="0">县/区</option>
                        <option>武侯区</option>
                    </select>
                    <input type="text" placeholder="经销商名称" class="control">
                    <button class=" btn btn-cancel search">搜索</button>
                </div>
            </div>
            <div class="row list-penal">
                @foreach($shops as $shop)
                <div class="col-sm-3 commodity new-listing">
                    <div class="img-wrap">
                        <img class="commodity-img" src="{{ $shop->likeable->image_url }}">
                        <span class="prompt new-listing"></span>
                    </div>
                    <p class="commodity-name">{{ $shop->likeable->name }}</p>
                    <p class="sell-panel">
                        <span class="money">最低配送额:￥{{ $shop->likeable->min_money }}</span>
                        <span class="sales pull-right">订单量 : {{ $shop->likeable->orders }}</span>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop
