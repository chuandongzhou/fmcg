@extends('index.menu-master')

@include('includes.address')

@section('subtitle', '业务管理-业务员客户添加')

@section('right')
    <form class="form-horizontal ajax-form"
          action="{{ url('api/v1/business/salesman-customer/' . $salesmanCustomer->id) }}"
          method="{{ $salesmanCustomer->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('business/salesman-customer') }}" autocomplete="off">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="username">客户名称:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="name" name="name" placeholder="请输入客户名称"
                       value="{{ $salesmanCustomer->name }}"
                       type="text">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="salesman_id">营业员:</label>

            <div class="col-sm-10 col-md-6">
                <select class="form-control" id="salesman_id" name="salesman_id">
                    <option value="">请选择业务员</option>
                    @foreach($salesmen as $key=>$salesman)
                        <option value="{{ $key }}" {{ $key ==  $salesmanCustomer->salesman_id ? 'selected' : '' }}> {{ $salesman }}</option>
                    @endforeach
                </select>

            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="contact">联系人:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="contact" name="contact" placeholder="请输入联系人"
                       value="{{ $salesmanCustomer->contact }}"
                       type="text">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="contact_information">联系方式:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="contact_information" name="contact_information" placeholder="联系方式"
                       value="{{ $salesmanCustomer->contact_information }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="business_area">营业面积:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="business_area" name="business_area" placeholder="营业面积"
                       value="{{ $salesmanCustomer->business_area }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="display_fee">陈列费用:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="display_fee" name="display_fee" placeholder="陈列费用"
                       value="{{ $salesmanCustomer->display_fee }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="platform_id">平台ID:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="platform_id" name="platform_id" placeholder="平台ID"
                       value="{{ $salesmanCustomer->platform_id }}"
                       type="text">
            </div>
        </div>

        <div class="form-group address-panel">
            <label class="col-sm-2 control-label">营业地址</label>

            <div class="col-sm-3">
                <select data-group="business_address" name="business_address[province_id]"
                        data-id="{{ $salesmanCustomer->address ? $salesmanCustomer->address->province_id : '' }}"
                        class="address-province form-control address">
                </select>
            </div>
            <div class="col-sm-3">
                <select data-group="business_address" name="business_address[city_id]"
                        data-id="{{  $salesmanCustomer->address ? $salesmanCustomer->address->city_id : '' }}"
                        class="address-city form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="business_address" name="business_address[district_id]"
                        data-id="{{ $salesmanCustomer->address ? $salesmanCustomer->address->district_id : '' }}"
                        class="address-district form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="business_address" name="business_address[street_id]"
                        data-id="{{ $salesmanCustomer->address ? $salesmanCustomer->address->street_id : '' }}"
                        class="address-street form-control address"></select>
            </div>
            <div class="hidden address-text">
                <input type="hidden" name="business_address[area_name]" class="area_name"
                       value="{{ $salesmanCustomer->address ? $salesmanCustomer->address->area_name : '' }}"/>
                <input type="hidden" class="lng" name="business_address_lng"
                       value="{{ $salesmanCustomer->business_address_lng }}"/>
                <input type="hidden" class="lat" name="business_address_lat"
                       value="{{ $salesmanCustomer->business_address_lat }}"/>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址</label>

            <div class="col-sm-10 col-md-6">

                <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                       class="form-control"
                       value="{{ $salesmanCustomer->address ? $salesmanCustomer->address->address : '' }}">

                <div id="address-map" class="baidu-map" data-lng="{{ $salesmanCustomer->business_address_lng }}"
                     data-lat="{{ $salesmanCustomer->business_address_lat }}">

                </div>
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

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

            $('select.address').change(function () {
                var obj = $(this), addressPanel = obj.closest('.address-panel'),
                        areaName = addressPanel.find('.area-name'),
                        provinceControl = addressPanel.find('select.address-province'),
                        cityControl = addressPanel.find('select.address-city'),
                        districtControl = addressPanel.find('select.address-district'),
                        streetControl = addressPanel.find('select.address-street'),
                        provinceVal = provinceControl.val() ? provinceControl.find("option:selected").text() : '',
                        cityVal = cityControl.val() ? cityControl.find("option:selected").text() : '',
                        districtVal = districtControl.val() ? districtControl.find("option:selected").text() : '',
                        streetVal = streetControl.val() ? streetControl.find("option:selected").text() : '';
                areaName.val(provinceVal + cityVal + districtVal + streetVal);
            })
        })
    </script>
@stop
