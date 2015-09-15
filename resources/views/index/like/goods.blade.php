@extends('index.menu-master')
@section('right')
<div class="container my-goods index">
    <div class="row">
        <div class="col-sm-10 collect search-page">
            <div class="row">
                <div class="col-sm-12">
                    <p class="search-list-item">
                        <label>类别 : </label>
                        <a href="#">全部</a>
                        <a href="#">茶饮料</a>
                        <a href="#">牛奶</a>
                    </p>
                    <p class="search-list-item">
                        <label>包装 : </label>
                        <a href="#" class="active">全部</a>
                        <a href="#">箱装</a>
                        <a href="#">优惠装</a>
                    </p>
                </div>
                <div class="col-sm-12 control-panel">
                    <label>配送区域</label>
                    <select class="control" name="province_id">
                        <option value="0">省</option>
                        <option>四川省</option>
                    </select>
                    <select class="control" name="city_id">
                        <option value="0">市</option>
                        <option>成都市</option>
                    </select>
                    <select class="control" name="county_id">
                        <option value="0">县/区</option>
                        <option>武侯区</option>
                    </select>
                    <input type="text" placeholder="经销商名称" class="control">
                    <button class=" btn btn-cancel search">搜索</button>
                </div>
            </div>
            <div class="row list-penal">
                @foreach($goods as $good)
                <div class="col-sm-3 commodity new-listing">
                    <div class="img-wrap">
                        <img class="commodity-img" src="{{ $good->likeable->image_url }}">
                        <span class="prompt new-listing"></span>
                    </div>
                    <p class="commodity-name">{{ $good->likeable->name }}</p>
                    <p class="sell-panel">
                        <span class="money">￥{{ $good->likeable->price }}</span>
                        <span class="sales pull-right">销量 : {{ $good->likeable->sales_volume }}</span>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop