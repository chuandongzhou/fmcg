@extends('index.switch')
@section('right')
    <div class="col-sm-10 collect">
        <div class="row">
            <div class="col-sm-12 control-panel">
                <label>配送区域</label>
                <select class="control">
                    <option>省</option>
                    <option>四川省</option>
                </select>
                <select class="control">
                    <option>市</option>
                    <option>成都市</option>
                </select>
                <select class="control">
                    <option>区</option>
                    <option>武侯区</option>
                </select>
                <input type="text" placeholder="经销商名称" class="control">
                <button class=" btn btn-cancel search">搜索</button>
            </div>
        </div>
        <div class="row list-penal">
            <div class="col-sm-3 commodity new-listing">
                <div class="img-wrap">
                    <img class="commodity-img" src="http://placehold.it/200">
                    <span class="prompt new-listing"></span>
                </div>
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <div class="img-wrap">
                    <img class="commodity-img" src="http://placehold.it/200">
                    <span class="prompt promotions"></span>
                </div>
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">最低配送额 :￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <div class="img-wrap">
                    <img class="commodity-img" src="http://placehold.it/200">
                    <span class="prompt lack"></span>
                </div>
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">最低配送额 :￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>
            <div class="col-sm-3 commodity">
                <div class="img-wrap"><img class="commodity-img" src="http://placehold.it/200"></div>
                <p class="commodity-name">商品名称商品名称商品名称商品名称商品名称商品名称</p>
                <p class="sell-panel">
                    <span class="money">最低配送额 :￥500</span>
                    <span class="sales pull-right">销量 : 2000</span>
                </p>
            </div>

        </div>
    </div>
@stop