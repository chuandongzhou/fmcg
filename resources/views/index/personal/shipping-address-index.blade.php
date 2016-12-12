@extends('index.menu-master')
@section('subtitle', '个人中心-收货地址')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <span class="second-level">收货地址</span>
@stop
@section('right')
    @include('includes.shipping-address')

    <form action="#" method="post">
        <div class="row shopping-address-wrap margin-clear">
            <div class="col-sm-12 table-responsive">
                <a class="add update-shipping-address" id="add-address" type="button"
                   data-target="#shippingAddressModal"
                   data-toggle="modal"><label><span class="fa fa-plus"></span></label>添加收货地址
                </a>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">默认收货地址</h3>
                    </div>
                    <div class="panel-container">
                        <table class="table table-bordered table-center">
                            <thead>
                            <tr>
                                <th>收货地址</th>
                                <th>联系人</th>
                                <th>联系电话</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($default as $defaultAddress)
                                <tr>
                                    <td>
                                        {{ $defaultAddress['address']['area_name'] . $defaultAddress['address']['address'] }}
                                    </td>
                                    <td>
                                        {{ $defaultAddress['consigner'] }}
                                    </td>
                                    <td>
                                        {{ $defaultAddress['phone'] }}
                                    </td>
                                    <td>
                                        <a class="edit update-shipping-address"
                                           data-target="#shippingAddressModal"
                                           data-toggle="modal"
                                           data-id="{{ $defaultAddress['id'] }}" data-consigner="{{ $defaultAddress['consigner'] }}"
                                           data-phone="{{ $defaultAddress['phone'] }}"
                                           data-province-id="{{ $defaultAddress['address']['province_id']  }}"
                                           data-city-id="{{ $defaultAddress['address']['city_id'] }}"
                                           data-district-id="{{ $defaultAddress['address']['district_id'] }}"
                                           data-street-id="{{ $defaultAddress['address']['street_id'] }}"
                                           data-area-name="{{ $defaultAddress['address']['area_name'] }}"
                                           data-address="{{ $defaultAddress['address']['address'] }}"
                                           data-x-lng="{{ $defaultAddress['x_lng'] }}" data-y-lat="{{ $defaultAddress['y_lat'] }}"><i
                                                    class="iconfont icon-xiugai"></i> 编辑</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 table-responsive">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">备用收货地址</h3>
                    </div>
                    <div class="panel-container">
                        <table class="table-bordered table table-center">
                            <thead>
                            <tr>
                                <th>收货地址</th>
                                <th>联系人</th>
                                <th>联系电话</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shippingAddress as $address)
                                <tr>
                                    <td>
                                        {{ $address['address']['area_name'] . $address['address']['address'] }}
                                    </td>
                                    <td>
                                        {{ $address['consigner'] }}
                                    </td>
                                    <td>
                                        {{ $address['phone'] }}
                                    </td>
                                    <td>
                                        <a class="ajax set-default"
                                           data-url="{{ url('api/v1/personal/shipping-address/default/'.$address['id']) }}"
                                           data-method="put">
                                            <i class="iconfont icon-qiyong"></i>设置为默认
                                        </a>
                                        <a class="edit update-shipping-address"
                                           data-target="#shippingAddressModal"
                                           data-toggle="modal"
                                           data-id="{{ $address['id'] }}" data-consigner="{{ $address['consigner'] }}"
                                           data-phone="{{ $address['phone'] }}"
                                           data-province-id="{{ $address['address']['province_id']  }}"
                                           data-city-id="{{ $address['address']['city_id'] }}"
                                           data-district-id="{{ $address['address']['district_id'] }}"
                                           data-street-id="{{ $address['address']['street_id'] }}"
                                           data-area-name="{{ $address['address']['area_name'] }}"
                                           data-address="{{ $address['address']['address'] }}"
                                           data-x-lng="{{ $address['x_lng'] }}" data-y-lat="{{ $address['y_lat'] }}"><i
                                                    class="iconfont icon-xiugai"></i> 编辑</a>
                                        <a class="ajax red"
                                           data-url="{{ url('api/v1/personal/shipping-address/'.$address['id']) }}"
                                           data-method="delete"><i class="iconfont icon-shanchu"></i>删除</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </form>
    @parent
@stop
