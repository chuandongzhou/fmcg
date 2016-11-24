@extends('index.menu-master')

@section('subtitle', '业务管理-业务员客户添加')

@include('includes.timepicker')
@include('includes.customer-mortgage-goods')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    <a href="{{ url('business/salesman-customer') }}">客户管理</a> &rarr;
    客户{{ $salesmanCustomer->id ? '编辑' : '新增' }}
@stop

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
            <label class="col-sm-2 control-label" for="salesman_id">业务员:</label>

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
            <label class="col-sm-2 control-label" for="platform_id">平台账号:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="account" name="account" placeholder="平台账号"
                       value="{{ $salesmanCustomer->account }}"
                       type="text">
            </div>
        </div>
        {{--营业地址--}}
        <div class="form-group address-panel">
            <label class="col-sm-2 control-label">营业地址:</label>

            <div class="col-sm-3">
                <select data-group="business_address" name="business_address[province_id]"
                        data-id="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->province_id : '' }}"
                        class="address-province form-control address">
                </select>
            </div>
            <div class="col-sm-3">
                <select data-group="business_address" name="business_address[city_id]"
                        data-id="{{  $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->city_id : '' }}"
                        class="address-city form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="business_address" name="business_address[district_id]"
                        data-id="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->district_id : '' }}"
                        class="address-district form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="business_address" name="business_address[street_id]"
                        data-id="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->street_id : '' }}"
                        class="address-street form-control address"></select>
            </div>
            <div class="hidden address-text">
                <input type="hidden" name="business_address[area_name]" class="area-name"
                       value="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->area_name : '' }}"/>
                <input type="hidden" class="lng" name="business_address_lng"
                       value="{{ $salesmanCustomer->business_address_lng }}"/>
                <input type="hidden" class="lat" name="business_address_lat"
                       value="{{ $salesmanCustomer->business_address_lat }}"/>
            </div>
        </div>
        {{--营业详细地址--}}
        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址:</label>

            <div class="col-sm-10 col-md-6">

                <input type="text" placeholder="请输入详细地址" name="business_address[address]"
                       class="form-control"
                       value="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->address : '' }}">
            </div>

        </div>

        <div class="form-group">
            <label for="address" class="col-sm-2 control-label"></label>
            <div class="col-sm-10 col-md-6">
                <div data-group="business_address" class="baidu-map" id="business_address"
                     data-lng="{{ $salesmanCustomer->business_address_lng }}"
                     data-lat="{{ $salesmanCustomer->business_address_lat }}"
                >
                </div>
            </div>
        </div>

        {{--收货地址--}}
        <div class="form-group address-panel">
            <label class="col-sm-2 control-label">收货地址:</label>

            <div class="col-sm-3">
                <select data-group="shipping_address" name="shipping_address[province_id]"
                        data-id="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->province_id : '' }}"
                        class="address-province form-control address">
                </select>
            </div>
            <div class="col-sm-3">
                <select data-group="shipping_address" name="shipping_address[city_id]"
                        data-id="{{  $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->city_id : '' }}"
                        class="address-city form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="shipping_address" name="shipping_address[district_id]"
                        data-id="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->district_id : '' }}"
                        class="address-district form-control address">
                </select>
            </div>
            <div class="col-sm-2">
                <select data-group="shipping_address" name="shipping_address[street_id]"
                        data-id="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->street_id : '' }}"
                        class="address-street form-control address"></select>
            </div>
            <div class="hidden address-text">
                <input type="hidden" name="shipping_address[area_name]" class="area-name"
                       value="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->area_name : '' }}"/>
                <input type="hidden" class="lng" name="shipping_address_lng"
                       value="{{ $salesmanCustomer->shipping_address_lng }}"/>
                <input type="hidden" class="lat" name="shipping_address_lat"
                       value="{{ $salesmanCustomer->shipping_address_lat }}"/>
            </div>
        </div>
        {{--收货详细地址--}}
        <div class="form-group">
            <label for="address" class="col-sm-2 control-label">详细地址:</label>

            <div class="col-sm-10 col-md-6">

                <input type="text" placeholder="请输入详细地址" name="shipping_address[address]"
                       class="form-control"
                       value="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->address : '' }}">

            </div>

        </div>

        <div class="form-group">
            <label for="address" class="col-sm-2 control-label"></label>
            <div class="col-sm-10 col-md-6">
                <div data-group="shipping_address" class="baidu-map" id="shipping_address"
                     data-lng="{{ $salesmanCustomer->shipping_address_lng }}"
                     data-lat="{{ $salesmanCustomer->shipping_address_lat }}"
                ></div>
            </div>
        </div>


        <div class="form-group row">
            <label class="col-sm-2 control-label" for="display_fee">陈列费:</label>

            <div class="col-md-2">
                <select name="display_type" class="form-control visible-select">
                    @foreach(cons()->valueLang('salesman.customer.display_type') as $type => $name)
                        <option value="{{ $type }}" {{ $type == $salesmanCustomer->display_type ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row visible-item visible-item-1 visible-item-2 {{ $salesmanCustomer->display_type ? '' : 'hidden' }}">
            <label class="col-sm-2 control-label" for="display_fee">有效时间:</label>

            <div class="col-sm-2">
                <input {{ $salesmanCustomer->display_type ? '' : 'disabled' }} class="form-control datetimepicker"
                       id="display_start_month" data-format="YYYY-MM"
                       name="display_start_month" placeholder="有效开始月份"
                       value="{{ $salesmanCustomer->display_start_month }}"
                       type="text">
            </div>
            <label class="control-label pull-left">至</label>
            <div class="col-sm-2">
                <input {{ $salesmanCustomer->display_type ? '' : 'disabled' }} class="form-control datetimepicker"
                       id="display_end_month" data-format="YYYY-MM"
                       name="display_end_month" placeholder="有效结束月份"
                       value="{{ $salesmanCustomer->display_end_month }}"
                       type="text">
            </div>
        </div>
        <div class="form-group row cash visible-item visible-item-1 {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.cash') ? '' : 'hidden' }}">
            <label class="col-sm-2 control-label" for="display_fee">现金:</label>

            <div class="col-md-2">
                <input {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.cash') ? '' : 'disabled' }}
                       class="form-control"
                       name="display_fee" placeholder="陈列费用"
                       value="{{ $salesmanCustomer->display_fee }}"
                       type="text">
            </div>
        </div>

        <div class="form-group row mortgage-goods visible-item visible-item-2 {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.mortgage') ? '' : 'hidden' }}">
            <label class="col-sm-2 control-label" for="display_fee">抵费商品:</label>

            <div class="col-sm-10">
                <input class="btn btn-default" data-toggle="modal" data-target="#mortgageGoodsModal" value="设置商品"
                       type="button">

                <ul class="mortgage-goods-group">
                    @foreach($salesmanCustomer->mortgageGoods as $mortgageGoods)
                        <li class="clearfix">
                            <label class="control-label col-sm-4 text-left-important">{{ $mortgageGoods->goods_name }}</label><label
                                    class="control-label col-sm-3">{{ (int)$mortgageGoods->pivot->total  . cons()->valueLang('goods.pieces', $mortgageGoods->pieces) }}</label>
                        </li>
                    @endforeach
                </ul>
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
            var baiduMap = initMap();
            addressSelectChange(true, baiduMap);
            visibleSelect();
        });
    </script>
@stop
