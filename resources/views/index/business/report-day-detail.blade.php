@extends('index.menu-master')
@include('includes.timepicker')
@include('includes.salesman-customer-route-map')
@section('subtitle', '业务管理-业务报告详细')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> >
    <a href="{{ url('business/report') }}">业务员报告</a> >
    <span class="second-level">业务报告明细</span>
@stop

@section('right')
    <div class="row sales-details-panel">
        <div class="col-sm-12 form-group salesman-controls">
            <a href="javascript:history.back()" class="btn btn-border-blue"><i class="iconfont icon-fanhui"></i>返回</a>
            <a class="btn btn-border-blue customer-map" href="javascript:" data-target="#customerAddressMapModal"
               data-toggle="modal">
                <i class="fa fa-map-marker"></i> 线路图
            </a>
            <a href="{{ url("business/report/{$salesman->id}/export?start_date={$startDate}&end_date={$endDate}") }}"
               class="btn btn-border-blue"><i class="iconfont icon-xiazai"></i>下载打印</a>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <b>{{ $salesman->name }} - 业务报告</b>
                        <span>{{ $startDate }}</span>
                    </h3>
                </div>
                <div class="panel-container">
                    <table class="table table-bordered table-center public-table">
                        <thead>
                        <tr>
                            <th>拜访客户数</th>
                            <th>退货单数</th>
                            <th>退货金额</th>
                            <th>拜访订货单数</th>
                            <th>拜访订货金额</th>
                            <th>自主订货单数</th>
                            <th>自主订货金额</th>
                            <th>总订货单数</th>
                            <th>总订货金额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ count($visitData) }}</td>
                            <td>{{ $visitStatistics['return_order_count'] or 0 }}</td>
                            <td class="red">{{ $visitStatistics['return_order_amount'] or 0 }}</td>
                            <td>{{ $visitStatistics['order_form_count'] or 0  }}</td>
                            <td class="red">{{ $visitStatistics['order_form_amount'] or 0 }}</td>
                            <td>{{ $platFormOrdersList->count() }}</td>
                            <td class="red">{{ $platFormOrdersList->sum('amount') }}</td>
                            <td>{{ isset($visitStatistics['order_form_count']) ?  $visitStatistics['order_form_count'] + $platFormOrdersList->count() : $platFormOrdersList->count() }}</td>
                            <td class="red">{{ isset($visitStatistics['order_form_amount']) ? bcadd($visitStatistics['order_form_amount'], $platFormOrdersList->sum('amount') , 2) : $platFormOrdersList->sum('amount') }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="">
                        <ul id="myTab" class="nav nav-tabs notice-bar padding-clear">
                            <li class="active">
                                <a href="#home" data-toggle="tab">
                                    拜访
                                </a>
                            </li>
                            <li><a href="#ios" data-toggle="tab">自主</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade in active" id="home">
                                @foreach($visitData  as  $customerId => $visit)
                                    <table class="table table-center table-bordered margin-clear business-table">
                                        <thead>
                                        <tr>
                                            <th colspan="5" class="title-blue">客户信息</th>
                                        </tr>
                                        <tr>
                                            <th>客户编号</th>
                                            <th>店铺名称</th>
                                            <th>联系人</th>
                                            <th>联系电话</th>
                                            <th>营业地址</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{ $customerId }}</td>
                                            <td>{{ $visit['customer_name'] }}</td>
                                            <td>{{ $visit['contact'] }}</td>
                                            <td>{{ $visit['contact_information'] }}</td>
                                            <td> {{ $visit['shipping_address_name'] }}
                                                <input type="hidden" class="map-data"
                                                       data-business-lng="{{ $visit['business_address_lng'] }}"
                                                       data-business-lat="{{ $visit['business_address_lat'] }}"
                                                       data-lng="{{ $visit['lng'] }}"
                                                       data-lat="{{ $visit['lat'] }}"
                                                       data-number="{{ $visit['visit_id'] }}"
                                                       data-name="{{ $visit['customer_name'] }}"
                                                ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    @if(isset($visit['display_fee']))
                                        <table class="table table-center table-bordered margin-clear">
                                            <thead>
                                            <tr>
                                                <th colspan="3" class="title">陈列费（现金）</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th>月份</th>
                                                <th>拜访时间</th>
                                                <th>金额</th>

                                            </tr>
                                            @foreach($visit['display_fee'] as $displayFee)
                                                <tr>
                                                    <td >{{ $displayFee['month'] }}</td>
                                                    <td>{{ $displayFee['created_at'] }}</td>
                                                    <td>{{ $displayFee['display_fee'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                    @if(isset($visit['mortgage']))
                                        <table class="table table-center table-bordered margin-clear">
                                            <thead>
                                            <tr>
                                                <th colspan="5" class="title">陈列费（货抵）</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th>月份</th>
                                                <th>拜访时间</th>
                                                <th>商品名称</th>
                                                <th>商品单位</th>
                                                <th>数量</th>
                                            </tr>
                                            @foreach($visit['mortgage'] as $date=>$mortgages)
                                                @foreach($mortgages as $mortgage)
                                                    <tr>
                                                        @if ($mortgage == head($mortgages))
                                                            <td rowspan="{{ count($mortgages) }}">{{ $date }}</td>
                                                        @endif
                                                        <td>{{ $mortgage['created_at'] }}</td>
                                                        <td>{{ $mortgage['name'] }}</td>
                                                        <td>{{ cons()->valueLang('goods.pieces', $mortgage['pieces']) }}</td>
                                                        <td>{{ $mortgage['num'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                    @if(isset($visit['statistics']))
                                        <table class="table text-center table-bordered table-center">
                                            <thead>
                                            <tr>
                                                <th colspan="10" class="text-center title">销售统计</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th>商品ID</th>
                                                <th>商品名称</th>
                                                <th>商品库存</th>
                                                <th>生产日期</th>
                                                <th>商品单价</th>
                                                <th>订货数量</th>
                                                <th>订货总金额</th>
                                                <th>退货数量</th>
                                                <th>退货总金额(元)</th>
                                            </tr>
                                            @foreach($visit['statistics'] as $goodsId=>$statistics)
                                                <tr>
                                                    <td>{{ $goodsId }}</td>
                                                    <td>{{ $statistics['goods_name'] }}</td>
                                                    <td>{{ $statistics['stock'] }}</td>
                                                    <td>{{ $statistics['production_date'] }}</td>
                                                    <td>{{ $statistics['price'] or 0 }}
                                                        / {{ isset($statistics['pieces']) ? cons()->valueLang('goods.pieces', $statistics['pieces']) : '件' }}</td>
                                                    <td>{{ $statistics['order_num'] }}</td>
                                                    <td>{{ $statistics['order_amount'] }}</td>
                                                    <td>{{ $statistics['return_order_num'] }}</td>
                                                    <td>{{ $statistics['return_amount'] }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="10" class="text-right">
                                                    订单总金额:<b class="red">{{ $visit['amount'] }}</b>
                                                    退货总金额:{{ $visit['return_amount'] }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="ios">
                                @foreach($platFormOrders as $customer)
                                    <table class="table table-center table-bordered margin-clear">
                                        <thead>
                                        <tr>
                                            <th colspan="5" class="title-blue">客户信息</th>
                                        </tr>
                                        <tr>
                                            <th>客户编号</th>
                                            <th>店铺名称</th>
                                            <th>联系人</th>
                                            <th>联系电话</th>
                                            <th>营业地址</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{ $customer['number'] }}</td>
                                            <td>{{ $customer['shop_name'] }}</td>
                                            <td>{{ $customer['contact'] }}</td>
                                            <td>{{ $customer['contact_information'] }}</td>
                                            <td>{{ $customer['business_address'] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-center table-bordered margin-clear">
                                        <thead>
                                        <tr>
                                            <th colspan="4" class="title">订单信息</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>订单ID</th>
                                            <th>下单时间</th>
                                            <th>下单状态</th>
                                            <th>订单金额</th>
                                        </tr>
                                        @foreach($customer['orders'] as $order)
                                            <tr>
                                                <td>{{ $order->order_id }}</td>
                                                <td>{{ $order->created_at }}</td>
                                                <td>{{ $order->order_status_name }}</td>
                                                <td>{{ $order->amount }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table text-center table-bordered table-center">
                                        <thead>
                                        <tr>
                                            <th colspan="10" class="text-center title">销售统计</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>商品ID</th>
                                            <th>商品名称</th>
                                            <th>订货数量</th>
                                            <th>订货总金额</th>
                                        </tr>
                                        @foreach($customer['orderGoods'] as $id=>$orderGoods)
                                            <tr>
                                                <td> {{ $id }}</td>
                                                <td>{{ $orderGoods['name'] }}</td>
                                                <td>{{ $orderGoods['order_num'] }}</td>
                                                <td>{{ $orderGoods['order_amount'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        var customerMapData = function () {
            var mapData = [];
            $('.business-table  .map-data').each(function () {
                var obj = $(this), data = [];
                data['lng'] = obj.data('lng');
                data['lat'] = obj.data('lat');
                data['businessLng'] = obj.data('businessLng');
                data['businessLat'] = obj.data('businessLat');
                data['number'] = '序号' + obj.data('number');
                data['name'] = obj.data('name');
                mapData.push(data);
            });
            return mapData;
        };
    </script>
@stop
