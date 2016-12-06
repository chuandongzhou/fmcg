@extends('index.menu-master')
@section('subtitle', '个人中心-收货地址')

@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <a href="{{ url('personal/shipping-address') }}">收货地址</a> >
    <span class="second-level"> 收货地址编辑</span>
@stop

@section('right')
    <form class="form-horizontal ajax-form"
          action="{{ url('api/v1/personal/shipping-address/' . $shippingAddress->id) }}"
          method="{{ $shippingAddress->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          autocomplete="off">
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="card_number"><span class="red">*</span> 收货人:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="consigner" name="consigner" placeholder="请输入收货人"
                       value="{{ $shippingAddress->consigner }}" type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="card_holder"><span class="red">*</span> 联系电话:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="phone" name="phone" placeholder="请输入联系电话"
                       value="{{ $shippingAddress->phone }}" type="text">
            </div>
        </div>
        <div class="form-group shop-address">
            <label class="col-sm-2 control-label"><span class="red">*</span> 所在地</label>

            <div class="col-sm-3">
                <select name="province_id" class="address-province form-control address"
                        data-id="{{ $shippingAddress['address']['province_id']  }}">
                    <option value="" selected="selected">请选择省市/其他...</option>
                </select>
            </div>
            <div class="col-sm-3 pd-left-clear">
                <select name="city_id" class="address-city form-control address"
                        data-id="{{ $shippingAddress['address']['city_id'] }}">
                    <option value="" selected="selected">请选择城市...</option>
                </select>
            </div>
            <div class="col-sm-2 pd-left-clear">
                <select name="district_id" class="address-district form-control address"
                        data-id="{{ $shippingAddress['address']['district_id'] }}">
                    <option value="" selected="selected">请选择区/县...</option>
                </select>
            </div>
            <div class="col-sm-2 pd-left-clear">
                <select data-id="{{ $shippingAddress['address']['street_id'] }}" name="street_id"
                        class="address-street form-control  address" style="display: none;">
                    <option value="" selected="selected">请选择街道...</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址</label>

            <div class="col-sm-10 col-md-6">
                <input type="hidden" name="area_name" value="{{ $shippingAddress['address']['area_name'] }}">
                <input type="text" placeholder="请输入详细地址" name="address" id="address" class="form-control"
                       value="{{ $shippingAddress['address']['address'] }}">
                <input type="hidden" name="x_lng" value="{{ $shippingAddress->x_lng }}">
                <input type="hidden" name="y_lat" value="{{ $shippingAddress->y_lat }}">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-push-2 col-sm-10">
                <div id="address-map" style="margin-top: 20px; overflow: hidden; zoom: 1; position: relative; height: 350px; width: 100%; z-index: 0; color: rgb(0, 0, 0); text-align: left; background-color: rgb(243, 241, 236);">

                </div>
            </div>
        </div>
        <div class="modal-footer middle-footer text-center">
            <button type="submit" class="btn btn-success">提交</button>
        </div>
    </form>
    @parent
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $('select.address').change(function () {
                var provinceControl = $('select[name="province_id"]'),
                        cityControl = $('select[name="city_id"]'),
                        districtControl = $('select[name="district_id"]'),
                        streetControl = $('select[name="street_id"]'),
                        provinceVal = provinceControl.val() ? provinceControl.find("option:selected").text() : '',
                        cityVal = cityControl.val() ? cityControl.find("option:selected").text() : '',
                        districtVal = districtControl.val() ? districtControl.find("option:selected").text() : '',
                        streetVal = streetControl.val() ? streetControl.find("option:selected").text() : '';
                $('input[name="area_name"]').val(provinceVal + cityVal + districtVal + streetVal);
            })
            getShopAddressMap({!! $shippingAddress->x_lng or 0  !!}, {!! $shippingAddress->y_lat or 0  !!});
        })
    </script>
@stop
