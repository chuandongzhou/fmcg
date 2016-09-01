@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '业务管理-业务报告详细')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    <a href="{{ url('business/report') }}">业务员报告</a> &rarr;
    业务报告明细
@stop

@section('right')

    <div class="row business-report-monthly">
        <div class="col-xs-12 business-report-title">
            <h3 class="text-center">{{ $startDate }} 至 {{ $endDate }}</h3>
            <h5 class="text-center">{{ $salesman->name }} - 业务报告</h5>
            <hr>
            <div>
                <a href="{{ url("business/report/{$salesman->id}/export?start_date={$startDate}&end_date={$endDate}") }}"
                   class="btn btn-primary">导出</a>

                <a href="javascript:history.back()" class="btn btn-cancel">返回</a>
            </div>
        </div>
    </div>
    <div class="row sales-details-panel">
        <div class="col-xs-3">
            <div class="item"><span class="prompt">拜访客户数</span> : <span>{{ count($visitData) }}</span></div>
            <div class="item"><span class="prompt">总订货单数</span> :
                <span>{{ isset($visitStatistics['order_form_count']) ?  $visitStatistics['order_form_count'] + $platFormOrdersList->count() : $platFormOrdersList->count() }}</span>
            </div>
            <div class="item"><span class="prompt">总订货金额</span> :
                <span>{{ isset($visitStatistics['order_form_amount']) ? bcadd($visitStatistics['order_form_amount'], $platFormOrdersList->sum('amount') , 2) : $platFormOrdersList->sum('amount') }}</span>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="item"><span class="prompt">退货单数</span> :
                <span>{{ $visitStatistics['return_order_count'] or 0 }}</span></div>
            <div class="item"><span class="prompt">拜访订货单数</span> :
                <span>{{ $visitStatistics['order_form_count'] or 0  }}</span></div>
            <div class="item"><span class="prompt">拜访订货金额</span> :
                <span>{{ $visitStatistics['order_form_amount'] or 0 }}</span></div>
        </div>
        <div class="col-xs-4">
            <div class="item"><span class="prompt">退货金额</span> :
                <span>{{ $visitStatistics['return_order_amount'] or 0 }}</span></div>
            <div class="item"><span class="prompt">自主订货单数</span> : <span>{{ $platFormOrdersList->count() }}</span></div>
            <div class="item"><span class="prompt">自主订货金额</span> : <span>{{ $platFormOrdersList->sum('amount') }}</span>
            </div>
        </div>
    </div>

    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#visit" data-toggle="tab">拜访</a>
        </li>
        <li><a href="#platform" data-toggle="tab">自主</a></li>

    </ul>
    <div class="row">
        <div class="col-xs-12 tab-content" id="myTabContent">
            <div class="tab-pane fade in active" id="visit">
                @foreach($visitData  as  $customerId => $visit)
                    <table class="table text-center business-table">
                        <tr>
                            <td>客户编号</td>
                            <td>店铺名称</td>
                            <td>联系人</td>
                            <td>联系电话</td>
                            <td colspan="2">营业地址</td>
                        </tr>
                        <tr>
                            <td>{{ $customerId }}</td>
                            <td>{{ $visit['customer_name'] }}</td>
                            <td>{{ $visit['contact'] }}</td>
                            <td>{{ $visit['contact_information'] }}</td>
                            <td colspan="2">{{ $visit['shipping_address_name'] }}</td>
                        </tr>
                        @if(isset($visit['display_fee']))
                            <tr>
                                <td rowspan="{{ (count($visit['display_fee']) ? (count($visit['display_fee']) + 2) : 0) + (isset($visit['mortgage']) ? (count(array_flatten($visit['mortgage']))/5 + 2) : 0) }}">
                                    陈列费
                                </td>
                                <td colspan="5">现金</td>
                            </tr>
                            <tr>
                                <td>拜访时间</td>
                                <td colspan="4">金额</td>
                            </tr>
                            @foreach($visit['display_fee'] as $displayFee)
                                <tr>
                                    <td>{{ $displayFee['created_at'] }}</td>
                                    <td colspan="4">{{ $displayFee['display_fee'] }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(isset($visit['mortgage']))
                            <tr>
                                @if(!isset($visit['display_fee']))
                                    <td rowspan="{{ count(array_flatten($visit['mortgage']))/5 + 2 }}">
                                        陈列费
                                    </td>
                                @endif
                                <td colspan="5">货抵</td>
                            </tr>
                            <tr>
                                <td>拜访时间</td>
                                <td>商品名称</td>
                                <td>商品单位</td>
                                <td colspan="2">数量</td>
                            </tr>
                            @foreach($visit['mortgage'] as $date=>$mortgages)
                                @foreach($mortgages as $mortgage)
                                    <tr>
                                        @if ($mortgage == head($mortgages))
                                            <td rowspan="{{ count($mortgages) }}">{{ $date }}</td>
                                        @endif
                                        <td>{{ $mortgage['name'] }}</td>
                                        <td>{{ cons()->valueLang('goods.pieces', $mortgage['pieces']) }}</td>
                                        <td colspan="2">{{ $mortgage['num'] }}</td>

                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                        @if(isset($visit['statistics']))
                            <tr>
                                <td colspan="6">销售统计</td>
                            </tr>
                            <tr>
                                <td>商品ID</td>
                                <td>商品名称</td>
                                <td>订货数量</td>
                                <td>订货总金额</td>
                                <td>退货数量</td>
                                <td>退货总金额</td>
                            </tr>
                            @foreach($visit['statistics'] as $goodsId=>$statistics)
                                <tr>
                                    <td>{{ $goodsId }}</td>
                                    <td>{{ $statistics['goods_name'] }}</td>
                                    <td>{{ $statistics['order_num'] }}</td>
                                    <td>{{ $statistics['order_amount'] }}</td>
                                    <td>{{ $statistics['return_order_num'] or 0 }}</td>
                                    <td>{{ $statistics['return_amount'] or 0 }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6">
                                    <b>订货总金额 : {{ $visit['amount'] }}</b> &nbsp;&nbsp;<b>退货总金额
                                        : {{ $visit['return_amount'] }}</b>
                                </td>
                            </tr>
                        @endif
                    </table>
                @endforeach
            </div>
            <div class="tab-pane fade" id="platform">
                @foreach($platFormOrders as $customer)
                    <table class="table text-center business-table">
                        <tr>
                            <th>客户编号</th>
                            <th>店铺名称</th>
                            <th>联系人</th>
                            <th>联系电话</th>
                            <th>营业地址</th>
                        </tr>
                        <tr>
                            <td>{{ $customer['number'] }}</td>
                            <td>{{ $customer['shop_name'] }}</td>
                            <td>{{ $customer['contact'] }}</td>
                            <td>{{ $customer['contact_information'] }}</td>
                            <td>{{ $customer['business_address'] }}</td>
                        </tr>
                        <tr>
                            <th>订单ID</th>
                            <th colspan="2">下单时间</th>
                            <th>订单状态</th>
                            <th>订单金额</th>
                        </tr>
                        @foreach($customer['orders'] as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td colspan="2">{{ $order->created_at }}</td>
                                <td>{{ $order->order_status_name }}</td>
                                <td>{{ $order->amount }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5">总计：{{ $customer['orders']->sum('amount') }}</td>
                        </tr>
                        <tr>
                            <th colspan="2">商品名称</th>
                            <th colspan="2">订货数量</th>
                            <th colspan="1">订货金额</th>
                        </tr>
                        @foreach($customer['orderGoods'] as $orderGoods)
                            <tr>
                                <td colspan="2">{{ $orderGoods['name'] }}</td>
                                <td colspan="2">{{ $orderGoods['order_num'] }}</td>
                                <td>{{ $orderGoods['order_amount'] }}</td>
                            </tr>
                        @endforeach

                    </table>
                @endforeach
            </div>
        </div>
    </div>
@stop
