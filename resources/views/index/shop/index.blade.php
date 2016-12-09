@extends('index.index-master')
@include('includes.jquery-lazeload')
@section('subtitle')
    @if($type == 'supplier')
        供应商列表
    @elseif($type == 'wholesaler')
        批发商列表
    @else
        商家列表
    @endif
@stop

@section('container')
    <div class="container-wrap">
        <div class="container dealer-index index search-page contents">
            <div class="row">
                <div class="col-sm-12 pd-right-clear">
                    <div class="tab-title store-tab-title clearfix">
                        <p class="pull-left sequence">
                            <a href="{{ url('shop'. ($type ? '?type='.$type : '')) }}"
                               class="{{ $sort == '' ? 'active' : '' }} control">全部</a>
                            <a href="{{ url('shop/hot'. ($type ? '?type='.$type : '')) }}"
                               class="control {{ $sort == 'hot' ? 'active' : '' }}">热门</a>
                            <a href="{{ url('shop/new'. ($type ? '?type='.$type : '')) }}"
                               class="control {{ $sort == 'new' ? 'active' : '' }}">最新</a>
                        </p>

                        <p class="pull-left area">
                            <span>配送区域</span>
                            <select name="province_id" data-id="{{ $get['province_id'] or 0 }}"
                                    class="address-province address hide"></select>
                            <select name="city_id" data-id="{{ $get['city_id'] or 0 }}"
                                    class="address-city address hide    "></select>
                            <select name="district_id" data-id="{{ $get['district_id'] or 0 }}"
                                    class="address-district address"> </select>
                            <select name="street_id" data-id="{{ $get['street_id'] or 0 }}"
                                    class="address-street address useless-control"> </select>
                        </p>
                    </div>
                </div>
            </div>
            @foreach($shops  as $shop)
                <div class="row list-penal dealer-commodity-wrap">
                    <div class="col-sm-6 pd-right-clear">
                        <div class="thumbnail clearfix">
                            <div class="img-wrap pull-left">
                                <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                    <img class="commodity-img" src="{{ $shop->logo_url }}">
                                </a>
                            </div>
                            <div class="content-panel store-content" style="">
                                <div class="commodity-name item">
                                    <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                        {{ $shop->name }}
                                    </a>
                                </div>
                                <div class="item address-panel">
                                    <span class="prompt">店家地址 : </span>
                                    <span class="address">{{ $shop->address }}</span>
                                </div>
                                <div class="store-presentation item">
                                    <span class="prompt">店家介绍 : </span>
                                    <span title="{{ $shop->introduction }}">{{ $shop->introduction }}</span>
                                </div>
                                <div class="sell-panel item">
                                    <span class="sales prompt">最低配送额 : </span>
                                    <span class="money">¥{{ $shop->min_money }}</span>
                                </div>
                                <div class="order-count item">
                                    <div class="item-child">
                                        <span class="prompt">店铺销量 : </span>
                                        <span>{{ $shop->sales_volume }}</span>
                                    </div>
                                    <div class="item-child">
                                        <span class="prompt">共</span>{{ $shop->goods_count }}
                                        <span class="prompt">件商品</span>
                                    </div>
                                    <div class="item-child">
                                        <a href="javascript:"
                                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                           class="contact"><span class="iconfont icon-kefu "></span> 联系客服</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 padding-clear item-wrap">
                        @for($i=0;$i<4;$i++)
                            @if(isset($shop->goods[$i]))
                                <div class=" product-item ">
                                    <a href="{{ url('goods/' .$shop->goods[$i]->id) }}">
                                        <img class="commodity-img lazy"
                                             data-original="{{ $shop->goods[$i]->image_url }}">
                                        <span class="commodity-name">{{ $shop->goods[$i]->name }}</span>
                                    </a>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            @endforeach
            <div class="row">
                <div class="col-xs-12 text-right">
                    {!!  $shops->appends(array_except($get , ['province_id' ,'city_id']))->render()  !!}
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
        $('select[name="district_id"]').change(function () {
            var districtControl = $(this),
                    address = districtControl.val() ? '{!! empty(array_except($get ,  ['province_id' ,'city_id' ,'district_id' ])) ? '?' : '&' !!}district_id=' + districtControl.val() : '';
            var url = '{!! url('shop'  . (empty(array_except($get , ['province_id' ,'city_id' ,'district_id' ])) ? '' :  '?' . http_build_query(array_except($get ,  ['province_id' ,'city_id' ,'district_id' ])))) !!}' + address;
            location.href = url;
        })
    </script>
@stop