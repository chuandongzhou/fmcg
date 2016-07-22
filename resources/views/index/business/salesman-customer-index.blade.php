@extends('index.menu-master')
@include('includes.salesman-customer-map')
@section('subtitle', '业务管理-客户管理')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    客户管理
@stop

@section('right')
    <form class="form-horizontal" method="get" action="{{ url('business/salesman-customer') }}" autocomplete="off">
        <div class="col-sm-12 form-group">
            <span class="item control-item">
                <select name="salesman_id" class="inline-control">
                    <option value="">全部业务员</option>
                    @foreach($salesmen as $salesman)
                        <option value="{{ $salesman->id }}" {{ $salesman->id == $salesmanId ? 'selected' : '' }}>{{ $salesman->name }}</option>
                    @endforeach
                </select>
            </span>
            <span class="item control-item">
               <input class="inline-control" type="text" name="name" value="{{ $name }}" placeholder="客户名称">
            </span>
            <span class="item control-item">
                <button type="submit" class="btn btn-default search-by-get">查询</button>
                <a class="btn btn-default" href="{{ url('business/salesman-customer/create') }}">新增客户</a>
                <a class="btn btn-default customer-map" href="javascript:" data-target="#customerAddressMapModal"
                   data-toggle="modal">
                    <i class="fa fa-map-marker"></i> 客户分布图
                </a>
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <table class="table table-bordered table-center table-middle salesman-customer-table">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>客户名称</th>
                        <th>平台ID</th>
                        <th>联系人</th>
                        <th>联系方式</th>
                        <th>营业地址</th>
                        <th>收货地址</th>
                        <th>营业面积</th>
                        <th>陈列费用</th>
                        <th>业务员</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>
                                {{ $customer->number }}
                            </td>
                            <td>
                                {{ $customer->name }}
                            </td>
                            <td>
                                {{ $customer->shop_id }}
                            </td>
                            <td>
                                {{ $customer->contact }}
                            </td>
                            <td>
                                {{ $customer->contact_information }}
                            </td>
                            <td width="120">
                                {{ $customer->business_address_name }}
                            </td>
                            <td width="120">
                                {{ $customer->shipping_address_name }}
                            </td>
                            <td>
                                {{ $customer->business_area }}
                            </td>
                            <td>
                                {{ $customer->display_fee }}
                            </td>
                            <td>
                                {{ $customer->salesman->name }}
                            </td>
                            <td>
                                <div role="group" class="btn-group btn-group-xs">
                                    <a class="btn btn-primary"
                                       href="{{ url('business/salesman-customer/' . $customer->id . '/edit') }}">
                                        <i class="fa fa-edit"></i> 编辑
                                    </a>

                                    <a class="btn btn-default"
                                       href="{{ url('business/salesman-customer/' . $customer->id) }}">
                                        <i class="fa fa-info"></i> 明细
                                    </a>

                                    <input type="hidden" class="map-data"
                                           data-lng="{{ $customer->business_address_lng }}"
                                           data-lat="{{ $customer->business_address_lat }}"
                                           data-number="{{ $customer->number }}"
                                           data-name="{{ $customer->name }}"
                                    >
                                </div>
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
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        var customerMapData = function () {
            var mapData = [];
            $('.salesman-customer-table  .map-data').each(function () {
                var obj = $(this), data = [];
                data['lng'] = obj.data('lng');
                data['lat'] = obj.data('lat');
                data['number'] =  '客户 ' + obj.data('number');
                data['name'] = obj.data('name');
                mapData.push(data);
            });
            return mapData;
        };
    </script>
@stop
