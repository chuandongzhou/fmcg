@extends('index.manage-master')

@section('subtitle', '业务管理-业务员客户添加')

@include('includes.timepicker')
@include('includes.customer-mortgage-goods', ['url' => url('api/v1/business/mortgage-goods')])

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <a href="{{ url('business/salesman-customer'.(is_null($customerType) ? '' : '?type=supplier'))}}"> {{is_null($customerType) ? '客户' : '供应商'}}管理</a> >
                    <span class="second-level">{{is_null($customerType) ? '客户' : '供应商'}}{{ $salesmanCustomer->id ? '编辑' : '新增' }}</span>
                </div>
            </div>
            <div class="row salesman">
                <div class="col-sm-12 create">
                    <form class="form-horizontal ajax-form"
                          action="{{ url('api/v1/business/salesman-customer/' . $salesmanCustomer->id) }}"
                          method="{{ $salesmanCustomer->id ? 'put' : 'post' }}"
                          data-help-class="col-sm-push-2 col-sm-10"
                          data-done-url="{{ url('business/salesman-customer') . (is_null($customerType) ? '' : '?type=supplier')}}"
                          autocomplete="off">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username"><span
                                        class="red">*</span>{{is_null($customerType) ? '客户' : '供应商'}}名称:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="name" name="name"
                                       placeholder="请输入{{is_null($customerType) ? '客户' : '供应商'}}名称"
                                       value="{{ $salesmanCustomer->name }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="salesman_id"><span
                                        class="red">*</span>业务员:</label>

                            <div class="col-sm-10 col-md-6">
                                <select class="@if($salesmanCustomer->id) white-bg @endif form-control"
                                        id="salesman_id"
                                        name="salesman_id">
                                    <option value="">请选择业务员</option>
                                    @foreach($salesmen as $key=>$salesman)
                                        <option value="{{ $key }}" {{ $key ==  $salesmanCustomer->salesman_id ? 'selected' : '' }}> {{ $salesman }}</option>
                                    @endforeach
                                </select>
                                {{--@if(auth()->user()->type == cons('user.type.maker') && $salesmanCustomer->id)
                                    <input type="hidden" name="salesman_id"
                                           value="{{$salesmanCustomer->salesman_id or ''}}">
                                @endif--}}
                            </div>
                        </div>
                        @if(($userType = auth()->user()->type) == cons('user.type.supplier') || $userType == cons('user.type.maker') )
                            @if(is_null($customerType))
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label" for="type">客户类型:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <select class="form-control" id="type" name="type">
                                            <option value="">请选择客户类型</option>
                                            @foreach(cons()->valueLang('user.type') as $type=>$name)
                                                @if($type < $userType && $type != cons('user.type.supplier'))
                                                    <option value="{{ $type }}" {{ $type ==  $salesmanCustomer->type ? 'selected' : '' }}> {{ $name }}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            @else
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label" for="salesman_id"><span
                                                class="red">*</span>业务区域:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <select class="@if($salesmanCustomer->id) white-bg @endif form-control"
                                                name="area_id">
                                            <option value="">请选择</option>
                                            @foreach($areas as $area)
                                                <option @if($area->id == $salesmanCustomer->area_id) selected
                                                        @endif value="{{$area->id}}">{{$area->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="{{cons('user.type.supplier')}}">
                                <input type="hidden" name="store_type"
                                       value="{{cons('salesman.customer.store_type.supermarket')}}">
                            @endif
                        @endif
                        @if(!$customerType)
                            <div id="store_type"
                                 class="form-group row @if($salesmanCustomer && $salesmanCustomer->type > cons('user.type.retailer')) hidden @endif">
                                <label class="col-sm-2 control-label" for="salesman_id"><span
                                            class="red">*</span>店铺类型:</label>

                                <div class="col-sm-10 col-md-6">
                                    <select class="@if($salesmanCustomer->id) white-bg @endif form-control"
                                            name="store_type">
                                        <option value="">请选择</option>
                                        @foreach(cons()->valueLang('salesman.customer.store_type') as $storeType => $typeName)
                                            <option @if(!is_null($salesmanCustomer->store_type) && $storeType == $salesmanCustomer->store_type) selected
                                                    @endif value="{{$storeType}}">{{$typeName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact"><span class="red">*</span>联系人:</label>

                            <div class="col-sm-6 col-md-4">
                                <input class="form-control" id="contact" name="contact" placeholder="请输入联系人"
                                       value="{{ $salesmanCustomer->contact }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact_information"><span class="red">*</span>
                                联系方式:</label>

                            <div class="col-sm-6 col-md-4">
                                <input class="form-control" id="contact_information" name="contact_information"
                                       placeholder="联系方式" value="{{ $salesmanCustomer->contact_information }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="business_area"><span class="red">*</span>
                                营业面积:</label>

                            <div class="col-sm-6 col-md-4">
                                <input class="form-control" id="business_area" name="business_area" placeholder="营业面积"
                                       value="{{ $salesmanCustomer->business_area }}"
                                       type="text">
                            </div>
                            <div class="col-sm-1 company">
                                平方米
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="platform_id">
                                @if(auth()->user()->type == cons('user.type.maker') && $customerType)
                                    <span class="red">*</span>
                                @endif 平台账号:
                            </label>

                            <div class="col-sm-6 col-md-4">
                                <input @if(auth()->user()->type == cons('user.type.maker') && $salesmanCustomer->id) readonly
                                       @endif class="form-control @if($salesmanCustomer->id) white-bg @endif"
                                       id="account" name="account" placeholder="平台账号"
                                       value="{{ $salesmanCustomer->account }}" type="text">
                            </div>
                        </div>
                        <div class="form-group address-panel">
                            <label class="col-sm-2 control-label"><span class="red">*</span> 营业地址:</label>

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
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label"><span class="red">*</span> 详细地址:</label>

                            <div class="col-sm-6 col-md-4">

                                <input type="text" placeholder="请输入详细地址" name="business_address[address]"
                                       class="form-control"
                                       value="{{ $salesmanCustomer->businessAddress ? $salesmanCustomer->businessAddress->address : '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label"></label>
                            <div class="col-sm-10 col-md-8">
                                <div data-group="business_address" class="baidu-map" id="business_address"
                                     data-lng="{{ $salesmanCustomer->business_address_lng }}"
                                     data-lat="{{ $salesmanCustomer->business_address_lat }}"
                                >
                                </div>
                            </div>
                        </div>
                        <div class="form-group address-panel">
                            <label class="col-sm-2 control-label"><span class="red">*</span> 收货地址:</label>

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
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="red">*</span> 详细地址:</label>

                            <div class="col-sm-6 col-md-4">
                                <input type="text" placeholder="请输入详细地址" name="shipping_address[address]"
                                       class="form-control"
                                       value="{{ $salesmanCustomer->shippingAddress ? $salesmanCustomer->shippingAddress->address : '' }}">
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10 col-md-8">
                                <div data-group="shipping_address" class="baidu-map" id="shipping_address"
                                     data-lng="{{ $salesmanCustomer->shipping_address_lng }}"
                                     data-lat="{{ $salesmanCustomer->shipping_address_lat }}"
                                ></div>
                            </div>
                        </div>


                        @if(auth()->user()->type < cons('user.type.maker'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">陈列费:</label>
                                <div class="col-sm-6 col-md-4">
                                    <select name="display_type" class="form-control visible-select">
                                        @foreach(cons()->valueLang('salesman.customer.display_type') as $type => $name)
                                            <option value="{{ $type }}" {{ $type == $salesmanCustomer->display_type ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group visible-item visible-item-1 visible-item-2 {{ $salesmanCustomer->display_type ? '' : 'hidden' }}">
                                <label class="col-sm-2 control-label" for="display_fee">有效时间:</label>
                                <div class="col-sm-4 col-md-3">
                                    <input {{ $salesmanCustomer->display_type ? '' : 'disabled' }} class="form-control datetimepicker"
                                           id="display_start_month" data-format="YYYY-MM"
                                           name="display_start_month" placeholder="有效开始月份"
                                           value="{{ $salesmanCustomer->display_start_month }}"
                                           type="text">
                                </div>
                                <div class="col-sm-1 col-md-1 company text-center">
                                    至
                                </div>
                                <div class="col-sm-4 col-md-3">
                                    <input {{ $salesmanCustomer->display_type ? '' : 'disabled' }} class="form-control datetimepicker"
                                           id="display_end_month" data-format="YYYY-MM"
                                           name="display_end_month" placeholder="有效结束月份"
                                           value="{{ $salesmanCustomer->display_end_month }}"
                                           type="text">
                                </div>
                            </div>
                            <div class="form-group cash visible-item visible-item-1 {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.cash') ? '' : 'hidden' }}">
                                <label class="col-sm-2 control-label">现金:</label>
                                <div class="col-sm-6 col-md-4">
                                    <input {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.cash') ? '' : 'disabled' }} name="display_fee"
                                           value="{{ $salesmanCustomer->display_fee }}" type="text" class="form-control"
                                           placeholder="请填写现金">
                                </div>
                            </div>
                            <div class="form-group display-goods mortgage-goods visible-item visible-item-2 {{ $salesmanCustomer->display_type == cons('salesman.customer.display_type.mortgage') ? '' : 'hidden' }}">
                                <label class="col-sm-2 control-label">陈列商品:</label>
                                <div class="col-sm-6 col-md-4 ">
                                    <button class="btn padding-clear" type="button" data-target="#mortgageGoodsModal"
                                            data-toggle="modal">设置商品
                                    </button>
                                    已选商品数<span
                                            class="red mortgage-goods-num">{{ count($salesmanCustomer->mortgageGoods) }}</span>个

                                    <div class="mortgage-goods-group hidden">
                                        @foreach($salesmanCustomer->mortgageGoods as $mortgageGoods)
                                            <input type="hidden" data-id="{{ $mortgageGoods->id }}"
                                                   name="{{ 'mortgage_goods['. $mortgageGoods->id.']' }}"
                                                   value="{{ (int)$mortgageGoods->pivot->total }}"/>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <div class="col-sm-push-2 col-sm-10 save">
                                <button class="btn btn-success" type="submit">提交</button>
                            </div>
                        </div>
                    </form>
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
        $(function () {
            var baiduMap = initMap();
            addressSelectChange(true, baiduMap);
            visibleSelect();
            $('#type').change(function () {
                $(this).find('option:selected').val() == "{{cons('user.type.retailer')}}" ? $('#store_type').removeClass('hidden') : $('#store_type').addClass('hidden')
            })
        });
    </script>
@stop
