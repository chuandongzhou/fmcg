@extends('mobile.master')

@section('subtitle', '收货地址')

@section('header')
    <div class="fixed-header fixed-item  orders-details-header">
        <div class="row nav-top white-bg">
            <div class="col-xs-12 color-black">{{ $shippingAddress->id ? '编辑' : '新增' }}地址</div>
        </div>
    </div>
@stop
@section('body')
    @parent
    <form class="mobile-ajax-form" action="{{ url('api/v1/personal/shipping-address/' . $shippingAddress->id ) }}"
          method="{{ $shippingAddress->id ? 'PUT' : 'POST' }}" data-done-url="{{ url('shipping-address') }}">
        <div class="container-fluid m60 p65 reg-container">
            <div class="row edit-wrap">
                <div class="col-xs-12 enter-panel">
                    <div class="item bordered">
                        <span class="control-label">收货人</span>
                        <input type="text" name="consigner" value="{{ $shippingAddress->consigner }}" class="pull-right"
                               placeholder="请输入收货人"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">联系电话</span>
                        <input type="text" class="pull-right" name="phone" value="{{ $shippingAddress->phone }}"
                               placeholder="请输入联系电话"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">所在地</span>
                        <div class="address pull-right">
                            <a id="txt_area">
                            <span id="address-area"
                                  @if ($shippingAddress->id) data-id="{{ $shippingAddress->address->province_id or 0 }},{{ $shippingAddress->address->city_id or 0 }},{{ $shippingAddress->address->district_id or 0 }}" @endif></span>
                                <i class="iconfont icon-jiantouyoujiantou right-arrow pull-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">街道</span>
                        <div class="address pull-right">
                            <a id="txt_street">
                            <span id="address-street" data-level="1"
                                  data-id="{{ $shippingAddress->address->street_id or 0 }}"></span>
                                <i class="iconfont icon-jiantouyoujiantou right-arrow pull-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">详细地址</span>
                        <input type="text" class="pull-right" name="address"
                               value="{{ $shippingAddress->address->address or '' }}" placeholder="请输入详细地址"/>
                    </div>
                </div>
                <div class="col-xs-12 set-panel clearfix">
                    <div class="pull-left prompt">设为默认地址</div>
                    <div class="pull-right">
                        <input type="checkbox" id="checkbox_c1" {{ $shippingAddress->is_default ? 'checked' : '' }} name="is_default" value="1" class="chk_3">
                        <label for="checkbox_c1"></label>
                    </div>
                </div>
                <div class="hidden">
                    <input type="hidden" name="province_id" value="{{ $shippingAddress->address->province_id or 0 }}">
                    <input type="hidden" name="city_id" value="{{ $shippingAddress->address->city_id or 0 }}">
                    <input type="hidden" name="district_id" value="{{ $shippingAddress->address->district_id or 0 }}">
                    <input type="hidden" name="street_id" value="{{ $shippingAddress->address->street_id or 0 }}">
                    <input type="hidden" name="area_name" value="{{ $shippingAddress->address->area_name or 0 }}">
                    <input type="hidden" name="x_lng" value="{{ $shippingAddress->x_lng or 0 }}">
                    <input type="hidden" name="y_lat" value="{{ $shippingAddress->y_lat or 0 }}">
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item white-bg address-footer">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script type="text/javascript" src="{{ asset('mobile/dialog.js') }}"></script>
    <script type="text/javascript" src="{{ asset('mobile/mobile-select-area.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        var json = formatAddress(addressData)
            , addressArea = $('#address-area')
            , addressStreet = $('#address-street')
            , addressName = ''
            , provinceInput = $('input[name="province_id"]')
            , cityInput = $('input[name="city_id"]')
            , districtInput = $('input[name="district_id"]')
            , streetInput = $('input[name="street_id"]')
            , areaNameInput = $('input[name="area_name"]')
            , xLngInput = $('input[name="x_lng"]')
            , yLatInput = $('input[name="y_lat"]');
        addressSelect(json, '#txt_area', addressArea, function (scroller, text, value) {
            addressStreet.html('');
            $('#txt_street').unbind('click');
            addressName = text.join('');
            addressArea.html(addressName);
            provinceInput.val(value[0]);
            cityInput.val(value[1]);
            districtInput.val(value[2]);
            streetInput.val(0);
            areaNameInput.val(addressName);
            xLngInput.val(0);
            yLatInput.val(0);
            if (value[2]) {
                setStreetArea(value[2], addressStreet, streetInput, areaNameInput, xLngInput, yLatInput);
            }
        });
        setStreetArea({{ $shippingAddress->address->district_id or 0 }}, addressStreet, streetInput, areaNameInput, xLngInput, yLatInput);
    </script>
@stop