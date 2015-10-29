@extends('index.menu-master')
@section('subtitle', '个人中心-收货地址')

@section('right')
    @include('index.personal.tabs')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <label>默认收货地址</label>
                    <a class="add" href="{{ url('personal/shipping-address/create') }}">
                        <label><span class="fa fa-plus"></span></label>
                        添加地址
                    </a>
                </div>
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
                                <a href="{{ url('personal/shipping-address/' . $defaultAddress['id'] . '/edit') }}"
                                   class="btn btn-success">编辑</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            <div class="col-sm-12 table-responsive">
                <div>
                    <label>备用收款账号</label>
                </div>
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
                                <a class="btn btn-primary ajax"
                                   data-url="{{ url('api/v1/personal/shipping-address-default/'.$address['id']) }}"
                                   data-method="put">
                                    设置为默认
                                </a>
                                <a href="{{ url('personal/shipping-address/' . $address['id'] . '/edit') }}"
                                   class="btn btn-success">编辑</a>
                                <a class="btn btn-cancel ajax"
                                   data-url="{{ url('api/v1/personal/shipping-address/'.$address['id']) }}"
                                   data-method="delete">删除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @parent
@stop
