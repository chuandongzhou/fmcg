@extends('index.menu-master')
@include('includes.salesman-customer-map')
@section('subtitle', '业务管理-客户管理')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> >
    <span class="second-level">客户管理</span>
@stop

@section('right')
    <div class="row salesman">
        <form class="form-horizontal" method="get" action="" autocomplete="off">
            <div class="col-sm-12 form-group salesman-controls">
                <a class="btn btn-blue-lighter" href="{{ url('business/salesman-customer/create') }}"><i
                            class="fa fa-plus"></i>新增客户</a>
                <select name="salesman_id" class="control">
                    <option value="">全部业务员</option>
                    @foreach($salesmen as $salesman)
                        <option value="{{ $salesman->id }}" {{ $salesman->id == $salesmanId ? 'selected' : '' }}>{{ $salesman->name }}</option>
                    @endforeach
                </select>
                <input class="control" type="text" name="name" value="{{ $name }}" placeholder="客户名称">
                <button type="submit" class="btn btn-blue-lighter search-by-get">查询</button>
                <a class="btn btn-border-blue customer-map" href="javascript:" data-target="#customerAddressMapModal"
                   data-toggle="modal">
                    <i class="fa fa-map-marker"></i> 客户分布图
                </a>
            </div>
            <div class="col-sm-12 table-responsive padding-clear">
                <table class="table table-bordered table-center table-middle public-table salesman-customer-table">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>客户名称</th>
                        <th>平台账号</th>
                        <th>联系人</th>
                        <th>联系方式</th>
                        <th>营业地址</th>
                        <th>收货地址</th>
                        <th>营业面积</th>
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
                                {{ $customer->account }}
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
                                {{ $customer->salesman->name }}
                            </td>
                            <td>
                                <div role="group" class="btn-group btn-group-xs">
                                    <a class=" edit"
                                       href="{{ url('business/salesman-customer/' . $customer->id . '/edit') }}">
                                        <i class="iconfont icon-xiugai"></i> 编辑
                                    </a><a class="edit" href="{{ url('business/salesman-customer/' . $customer->id) }}">
                                        <i class="iconfont icon-iconmingchengpaixu65"></i> 明细
                                    </a>

                                    <input type="hidden" class="map-data"
                                           data-lng="{{ $customer->business_address_lng }}"
                                           data-lat="{{ $customer->business_address_lat }}"
                                           data-number="{{ $customer->number }}"
                                           data-name="{{ $customer->name }}"
                                           data-id="{{ $customer->id }}"
                                    >
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
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
                data['number'] = '客户 ' + obj.data('number');
                data['name'] = obj.data('name');
                data['href'] = site.url('business/salesman-customer/' + obj.data('id'));
                mapData.push(data);
            });
            return mapData;
        };
    </script>
@stop
