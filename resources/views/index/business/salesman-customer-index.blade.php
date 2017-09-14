@extends('index.manage-master')
@include('includes.salesman-customer-map')
@section('subtitle', '业务管理-'.(empty($data['type']) ? '客户' : '供应商').'管理')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level">{{empty($data['type']) ? '客户' : '供应商'}}管理</span>
                </div>
            </div>
            <div class="row salesman">
                <form class="form-horizontal" method="get"
                      action="{{ url('business/salesman-customer') . (empty($data['type']) ? '' : '?type=supplier')}}"
                      autocomplete="off">
                    <div class="col-sm-12 form-group salesman-controls">
                        <a class="btn btn-blue-lighter"
                           href="{{ url('business/salesman-customer/create') . (empty($data['type']) ? '' : '?type=supplier')}}"><i
                                    class="fa fa-plus"></i>新增{{empty($data['type']) ? '客户' : '供应商'}}</a>
                        <select name="salesman_id" class="ajax-select control">
                            <option value="">全部业务员</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ isset($data['salesman_id']) && $salesman->id == $data['salesman_id'] ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                        @if(empty($data['type']))
                            店铺类型:
                            <select name="store_type" class="ajax-select control">
                                <option value="">全部</option>
                                @foreach(cons()->valueLang('salesman.customer.store_type') as $storeType => $typeName)
                                    <option {{ isset($data['store_type']) && $storeType == $data['store_type'] ? 'selected' : '' }} value="{{$storeType}}">{{$typeName}}</option>
                                @endforeach
                            </select>
                        @endif
                        @if(!empty($data['type']))
                            <select name="area_id" class="ajax-select control">
                                <option value="">全部区域</option>
                                @foreach($areas as $area)
                                    <option @if(isset($data['area_id']) &&$area->id == $data['area_id']) selected
                                            @endif value="{{$area->id}}">{{$area->name}}</option>
                                @endforeach
                            </select>
                        @endif
                        <input class="control" type="text" name="name"
                               value="{{ isset($data['name'])?$data['name']:'' }}"
                               placeholder="{{empty($data['type']) ? '客户' : '供应商'}}名称">
                        <button type="submit" class="btn btn-blue-lighter search-by-get">查询</button>
                        <a class="btn btn-border-blue customer-map" href="javascript:"
                           data-target="#customerAddressMapModal"
                           data-toggle="modal">
                            <i class="fa fa-map-marker"></i> {{empty($data['type']) ? '客户' : '供应商'}}分布图
                        </a>
                    </div>
                </form>
                <div class="col-sm-12 table-responsive padding-clear">
                    <table class="table table-bordered table-center table-middle public-table salesman-customer-table">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>{{empty($data['type']) ? '客户' : '供应商'}}名称</th>
                            <th>平台账号</th>
                            @if(empty($data['type']))
                                <th>店铺类型</th>
                            @endif
                            <th>联系人</th>
                            <th>联系方式</th>
                            <th>营业地址</th>
                            <th>收货地址</th>
                            <th>营业面积</th>
                            @if(!empty($data['type']))
                                <th>区域</th>
                            @endif
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
                                @if(empty($data['type']))
                                    <td>
                                        @if($customer->type == cons('user.type.retailer') && auth()->user()->type < cons('user.type.maker'))
                                            <i class="iconfont icon-xiugai warning-edit-icon"></i>
                                        @endif
                                        <i class="iconfont icon-baocun hidden warning-edit-icon"
                                           data-id="{{$customer->id}}"></i>
                                        <span class="store-type undisable"> {{$customer->type == cons('user.type.retailer') ? $customer->store_type_name : ''}} </span>
                                        <select class="form-control store-type modify hidden" id="store_type"
                                                name="store_type">
                                            @foreach(cons()->valueLang('salesman.customer.store_type') as $storeType => $typeName)
                                                <option @if(!is_null($customer->store_type) && $storeType == $customer->store_type) selected
                                                        @endif value="{{$storeType}}">{{$typeName}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endif
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
                                @if(!empty($data['type']))
                                    <td>
                                        {{ $customer->area_name}}
                                    </td>
                                @endif
                                <td>
                                    {{ $customer->salesman_name }}
                                </td>
                                <td>
                                    <div role="group" class="btn-group btn-group-xs">
                                        @if(!(auth()->user()->type == cons('user.type.maker') && empty($data['type'])))
                                            <a class=" edit"
                                               href="{{ url('business/salesman-customer/' . $customer->id . '/edit')  . (empty($data['type']) ? '' : '?type=supplier')}}">
                                                <i class="iconfont icon-xiugai"></i> 编辑
                                            </a>
                                        @endif
                                        <a class="edit"
                                           href="{{ url('business/salesman-customer/' . $customer->id.(empty($data['type']) ? '' : '?type=supplier')) }}">
                                            <i class="iconfont icon-iconmingchengpaixu65"></i> 明细</a>
                                        @if(!(auth()->user()->type == cons('user.type.maker') && empty($data['type'])))
                                            <a class="edit"
                                               href="{{ url('business/salesman-customer/' . $customer->id . '/bill'.(empty($data['type']) ? '' : '?type=supplier')) }}">
                                                <i class="iconfont icon-duizhangdan"></i> 对账单
                                            </a>
                                        @endif
                                        @if($customer->type == cons('user.type.supplier'))
                                            <a class="stock-query"
                                               href="{{ url('business/salesman-customer/' . $customer->id . '/stock') }}">
                                                <i class="iconfont icon-chaxun"></i> 库存查询
                                            </a>
                                        @endif

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
                    <div class="text-right">
                        {!! $customers->appends($data)->render() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
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
                data['number'] = obj.data('number');
                data['name'] = obj.data('name');
                data['href'] = site.url('business/salesman-customer/' + obj.data('id'));
                mapData.push(data);
            });
            return mapData;
        };

        /**
         * 修改店铺类型
         */
        $('.icon-xiugai').click(function () {
            var self = $(this);
            self.addClass('hidden').siblings('i').removeClass('hidden');
            self.siblings(".undisable").addClass("hidden").siblings(".modify").removeClass('hidden');
        });

        /**
         * 保存店铺类型
         */
        $('.icon-baocun').click(function () {
            var self = $(this),
                    select = self.siblings('select'),
                    id = self.data('id'),
                    store_type = $(select).find("option:selected").val();
            self.parents('td').html('<i class="fa fa-spinner fa-pulse"></i>');
            $data = {'store_type': store_type};
            $.ajax({
                url: site.api('business/salesman-customer/update-store-type/' + id),
                type: 'put',
                dataType: 'json',
                data: $data,
                success: function (data) {
                    location.reload();
                    /*self.addClass('hidden').siblings('i').removeClass('hidden');
                     self.siblings(".modify").addClass("hidden").siblings(".undisable").removeClass('hidden');
                     self.siblings('.warning_value').html(war_value);
                     self.siblings('.warning_piece').html($(select).find("option:selected").html());*/
                }
            })
        });
    </script>
@stop
