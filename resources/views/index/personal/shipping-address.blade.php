@extends('index.menu-master')
@section('subtitle', '个人中心-提现账号')

@section('right')
    @include('index.personal.tabs')
    <form class="form-horizontal ajax-form"
          action="{{ url('api/v1/personal/shipping-address/' . $shippingAddress->id) }}"
          method="{{ $shippingAddress->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/shipping-address') }}">
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="card_number">收货人:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="consigner" name="consigner" placeholder="请输入收货人"
                       value="{{ $shippingAddress->consigner }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="card_holder">联系电话:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="phone" name="phone" placeholder="请输入联系电话"
                       value="{{ $shippingAddress->phone }}"
                       type="text">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">所在地</label>

            <div class="col-sm-3">
                <select name="province_id" data-id="{{ $shippingAddress['address']['province_id']  }}"
                        class="address-province form-control address">
                    <option selected="selected" value="">请选择省市/其他...</option>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="city_id" data-id="{{ $shippingAddress['address']['city_id'] }}"
                        class="address-city form-control address">
                    <option selected="selected" value="">请选择城市...</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="district_id" data-id="{{ $shippingAddress['address']['district_id'] }}"
                        class="address-district form-control address">
                    <option selected="selected" value="">请选择区/县...</option>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="street_id" data-id="{{ $shippingAddress['address']['street_id'] }}"
                        class="address-street form-control address"></select>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址</label>

            <div class="col-sm-10 col-md-6">
                <input type="hidden" name="province_city_district" value="{{ $shippingAddress->province_city_district }}"/>
                <input type="text" placeholder="请输入详细地址" name="detail_address" id="detail_address" class="form-control"
                       value="{{ $shippingAddress['address']['detail_address'] }}">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-push-2 col-sm-10">
                <button class="btn btn-primary" type="submit">提交</button>
            </div>
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
                $('input[name="province_city_district"]').val(provinceVal + ' ' + cityVal + ' ' + districtVal + ' ' + streetVal);
            })
        })
    </script>
@stop
