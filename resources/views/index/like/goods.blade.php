@extends('index.menu-master')
@section('right')
<div class="my-goods index">
    <div class="row">
        <div class="col-sm-10 collect search-page">
            <div class="row">
                <div class="col-sm-12">
                    <p class="search-list-item">
                        <label>类别 : </label>
                        <a href="{{ url('like/goods?cate_level_2=0') }}" class="btn control {{ empty($data['cate_level_2']) ? 'active' : '' }}">全部</a>
                        @foreach($cateArr as $cate)
                        <a href="{{ url('like/goods?cate_level_2='.$cate['id']) }}" class="btn control {{ (isset($data['cate_level_2']) && $cate['id'] == $data['cate_level_2']) ? 'active' : '' }}">{{ $cate['name'] }}</a>
                        @endforeach
                    </p>
                    {{--<p class="search-list-item">--}}
                        {{--<label>包装 : </label>--}}
                        {{--<a href="#" class="active">全部</a>--}}
                        {{--<a href="#">箱装</a>--}}
                        {{--<a href="#">优惠装</a>--}}
                    {{--</p>--}}
                </div>
                <form action="{{ url('like/goods') }}" method="get">
                    <div class="col-sm-12 control-panel">
                        <label>配送区域</label>
                        <select data-id="{{ $data['province_id'] or 0 }}" class="control address-province" name="province_id"></select>
                        <select data-id="{{ $data['city_id'] or 0 }}" class="control address-city" name="city_id"></select>
                        <select data-id="{{ $data['district_id'] or 0 }}" class="control address-district" name="district_id"></select>
                        <select data-id="{{ $data['street_id'] or 0 }}" class="control address-street" name="street_id"></select>
                        <input type="text" placeholder="商家名称" class="control" name="name" value="{{ $data['name'] or '' }}">
                        <button class=" btn btn-cancel search">搜索</button>
                    </div>
                </form>
            </div>
            <div class="row list-penal">
                @foreach($goods as $good)
                    <a href="{{ url('goods/'.$good['id']) }}">
                        <div class="col-sm-3 commodity new-listing">
                            <div class="img-wrap">
                                <img class="commodity-img" src="{{ $good->image_url }}">
                                <span class="prompt new-listing"></span>
                            </div>
                            <div class="content-panel">
                                <p class="commodity-name">{{ $good->name }}</p>
                                <p class="sell-panel">
                                    <span class="money">￥{{ $good->price }}</span>
                                    <span class="sales pull-right">销量 : {{ $good->sales_volume }}</span>
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop
@section('js-lib')
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop