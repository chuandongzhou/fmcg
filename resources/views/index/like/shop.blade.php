@extends('index.menu-master')
@section('right')
<div class="row my-goods index">
    <div class="col-sm-12 collect">
        <div class="row">
            <div class="col-sm-12 control-panel">
                <form action="{{ url('like/shops') }}" method="get">
                    <label>配送区域</label>
                    <select data-id="{{ $data['province_id'] or 0 }}" class="control address-province" name="province_id"></select>
                    <select data-id="{{ $data['city_id'] or 0 }}" class="control address-city" name="city_id"> </select>
                    <select data-id="{{ $data['district_id'] or 0 }}" class="control address-district" name="district_id"> </select>
                    <select data-id="{{ $data['street_id'] or 0 }}" class="control address-street" name="street_id"> </select>

                    <input type="text" placeholder="商家名称" class="control" name="name" value="{{$data['name'] or '' }}">
                    <button class=" btn btn-cancel search">搜索</button>
                </form>
            </div>
        </div>
        @if(isset($shops))
            <div class="row list-penal">
                @foreach($shops as $shop)
                    <div class="col-sm-3 commodity">
                        <div class="img-wrap">
                            <a href="{{ url('shop/'.$shop['id'].'/detail') }}">
                                <img class="commodity-img" src="{{ $shop['image_url'] }}">
                                {{--<span class="prompt"></span>--}}
                            </a>
                        </div>
                        <div class="content-panel">
                            <a href="{{ url('shop/'.$shop['id'].'/detail') }}">
                                <p class="commodity-name">{{ $shop['name'] }}</p>

                                <p class="sell-panel">
                                    <span class="money">最低配送额:￥{{ $shop['min_money'] }}</span>
                                    <span class="sales pull-right">订单量 : {{ $shop['orders'] }}</span>
                                </p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
