@extends('index.index-master')

@section('subtitle', '首页')

@section('container')
    <div class="container dealer-index index search-page contents">
        <div class="row">
            <div class="col-sm-12">
                <div class="tab-title store-tab-title clearfix">
                    <p class="pull-left sequence">
                        <a href="{{ url('shop'. ($type ? '?type='.$type : '')) }}"
                           class="{{ $sort == '' ? 'active' : '' }} control">全部</a>
                        <a href="{{ url('shop/hot'. ($type ? '?type='.$type : '')) }}"
                           class="control {{ $sort == 'hot' ? 'active' : '' }}">热门</a>
                        <a href="{{ url('shop/new'. ($type ? '?type='.$type : '')) }}"
                           class="control {{ $sort == 'new' ? 'active' : '' }}">最新</a>
                    </p>

                    <p class="pull-right">
                        <span>配送区域</span>
                        <select name="province_id" data-id="{{ $address['province_id'] or 0 }}"
                                class="address-province address"></select>
                        <select name="city_id" data-id="{{ $address['city_id'] or 0 }}"
                                class="address-city address"></select>
                        <select name="district_id" data-id="{{ $address['district_id'] or 0 }}"
                                class="address-district address"> </select>
                        <select name="street_id" data-id="{{ $address['street_id'] or 0 }}"
                                class="address-street address"> </select>
                    </p>
                </div>
            </div>
        </div>
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
                            </p>

                            <p class="sell-panel item">
                                <span class="sales">最低配送额 : </span>
                                <span class="money">￥{{ $shop->min_money }}</span>
                            </p>

                            <p class="order-count item">销量 : <span>{{ $shop->sales_volume }}</span></p>

                            <p class="item order-count"><span>联系地址 : </span><span>{{ $shop->address }}</span></p>

                            <p class="item order-count store-presentation"><span>店铺介绍 : </span>
                                <span title="{{ $shop->introduction }}">{{ $shop->introduction }}</span>
                            </p>
                        </div>
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
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $('select.address').change(function () {
            var provinceControl = $('select[name="province_id"]'),
                    cityControl = $('select[name="city_id"]'),
                    districtControl = $('select[name="district_id"]'),
                    streetControl = $('select[name="street_id"]'),
                    type = '{{ $type }}',
                    address = type ? '?type=' + type : '',
                    join = type ? '&' : '?';
            address += provinceControl.val() ? join + 'province_id=' + provinceControl.val() : '';
            address += cityControl.val() ? '&city_id=' + cityControl.val() : '';
            address += districtControl.val() ? '&district_id=' + districtControl.val() : '';
            address += streetControl.val() ? '&street_id=' + streetControl.val() : '';
            var url = '{{ url('shop/' . $sort ) }}' + address;

            location.href = url;
        })
    </script>
@stop