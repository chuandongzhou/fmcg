@extends('index.menu-master')

@section('subtitle', '店铺收藏')
@section('top-title', '我的收藏->店铺收藏')
@section('right')
    <div class="row my-goods index">
        <div class="col-sm-12 collect">
            <div class="row">
                <div class="col-sm-12 control-panel">
                    <form action="{{ url('like/shops') }}" method="get" autocomplete="off">
                        <label>配送区域</label>
                        <select data-id="{{ $data['province_id'] or 0 }}" class="control address-province"
                                name="province_id"></select>
                        <select data-id="{{ $data['city_id'] or 0 }}" class="control address-city"
                                name="city_id"> </select>
                        <select data-id="{{ $data['district_id'] or 0 }}" class="control address-district"
                                name="district_id"> </select>
                        <select data-id="{{ $data['street_id'] or 0 }}" class="control address-street"
                                name="street_id"> </select>

                        <input type="text" placeholder="商家名称" class="control" name="name"
                               value="{{$data['name'] or '' }}">
                        <button class=" btn btn-cancel search search-by-get">搜索</button>
                    </form>
                </div>
            </div>
            @if(isset($shops))
                {{--<div class="row list-penal commodity-other">--}}
                    {{--@foreach($shops as $shop)--}}
                        {{--<div class="col-sm-3 commodity">--}}
                            {{--<div class="img-wrap">--}}
                                {{--<a href="{{ url('shop/'.$shop->id.'/detail') }}" target="_blank">--}}
                                    {{--<img class="commodity-img" src="{{ $shop->image_url }}">--}}
                                    {{--<span class="prompt"></span>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                            {{--<div class="content-panel">--}}
                                {{--<a href="{{ url('shop/'.$shop->id.'/detail') }}" target="_blank">--}}
                                    {{--<p class="commodity-name">{{ $shop->name }}</p>--}}

                                    {{--<p class="sell-panel">--}}
                                        {{--<span class="money">最低配送额:￥{{ $shop->min_money }}</span>--}}
                                        {{--<span class="sales pull-right">销量 : {{ $shop->sales_volume }}</span>--}}
                                    {{--</p>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                <div class="row list-penal dealer-commodity-wrap">
                    @foreach($shops  as $shop)
                        <div class="col-sm-6">
                            <div class="thumbnail clearfix">
                                <div class="img-wrap pull-left">
                                    <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                        <img class="commodity-img" src="{{ $shop->logo_url }}">
                                    </a>
                                </div>
                                <div class="content-panel store-content" style="">
                                    <p class="commodity-name item">
                                        <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                            {{ $shop->name }}
                                        </a>
                                        <a href="javascript:"
                                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                           class="contact"><span class="fa fa-commenting-o"></span> 联系客服</a>

                                    </p>

                                    <p class="sell-panel item">
                                        <span class="sales">最低配送额 : </span>
                                        <span class="money">￥{{ $shop->min_money }}</span>
                                    </p>

                                    <p class="order-count item">销量 : <span>{{ $shop->sales_volume }}</span></p>

                                    <p class="order-count store-presentation item"><span>店铺介绍 : </span>
                                        <span title="{{ $shop->introduction }}">{{ $shop->introduction }}</span>
                                    </p>

                                    <p class="item order-count address-panel"><span>联系地址 : </span><span
                                                class="address">{{ $shop->address }}</span></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row">
                <div class="col-xs-12 text-right">
                    {!! $shops->appends(array_filter($data))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop
