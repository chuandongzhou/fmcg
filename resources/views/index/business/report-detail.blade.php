@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '业务管理-业务报告详细')

@section('right')

    @if ($startDate == $endDate)
        <div class="row">
            <div class="col-xs-12 business-report-title">
                <h3 class="text-center">{{ $startDate }}</h3>
                <h5 class="text-center">{{ $salesman->name }}-业务报告</h5>
                <hr>
                <div>
                    <a href="" class="btn btn-cancel">线路图</a>
                    <a href="{{ url("business/report/2/export?start_date={$startDate}&end_date={$endDate}") }}" class="btn btn-primary">导出</a>
                </div>
            </div>
            <div class="col-xs-12">
                @foreach($visitData  as  $customerId => $visit)
                    <table class="table text-center business-table">
                        <tr>
                            <td>拜访序号</td>
                            <td>拜访时间</td>
                            <td>客户编号</td>
                            <td>店铺名称</td>
                            <td>联系人</td>
                            <td>联系电话</td>
                            <td colspan="3">营业地址</td>
                        </tr>

                        <tr>
                            <td>{{ $visit['visit_id'] }}</td>
                            <td>{{ $visit['created_at'] }}</td>
                            <td>{{ $customerId }}</td>
                            <td>{{ $visit['customer_name'] }}</td>
                            <td>{{ $visit['contact'] }}</td>
                            <td>{{ $visit['contact_information'] }}</td>
                            <td colspan="3">{{ $visit['shipping_address_name'] }}</td>
                        </tr>
                        @if(isset($visit['display_fee']))
                            <tr>
                                <td colspan="9">陈列费</td>
                            </tr>
                            <tr>
                                <td colspan="9">现金 : {{ $visit['display_fee'][0]['display_fee'] }}</td>
                            </tr>
                        @endif
                        @if(isset($visit['mortgage']))
                            <tr>
                                <td colspan="9">货抵</td>
                            </tr>
                            <tr>
                                <td colspan="3">商品名称</td>
                                <td colspan="3">商品单位</td>
                                <td colspan="3">数量</td>
                            </tr>
                            @foreach(head($visit['mortgage']) as $mortgage)
                                <tr>
                                    <td colspan="3">{{ $mortgage['name'] }}</td>
                                    <td colspan="3">{{ $mortgage['pieces'] }}</td>
                                    <td colspan="3">{{ $mortgage['num'] }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="9">客户销售商品</td>
                        </tr>
                        <tr>
                            <td>商品ID</td>
                            <td>商品名称</td>
                            <td>商品库存</td>
                            <td>生产日期</td>
                            <td>商品单价</td>
                            <td>订货数量</td>
                            <td>订货总金额</td>
                            <td>退货数量</td>
                            <td>退货总金额</td>
                        </tr>
                        @foreach($visit['statistics'] as $goodsId=>$statistics)
                            <tr>
                                <td>{{ $goodsId }}</td>
                                <td>{{ $statistics['goods_name'] }}</td>
                                <td>{{ $statistics['stock'] }}</td>
                                <td>{{ $statistics['production_date'] }}</td>
                                <td>{{ $statistics['price'] or 0 }}/{{ $statistics['pieces'] or '件' }}</td>
                                <td>{{ $statistics['order_num'] }}</td>
                                <td>{{ $statistics['order_amount'] }}</td>
                                <td>{{ $statistics['return_order_num'] }}</td>
                                <td>{{ $statistics['return_amount'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="9">
                                <b>订货总金额 : {{ $visit['amount'] }}</b> &nbsp;&nbsp;<b>退货总金额
                                    : {{ $visit['return_amount'] }}</b>
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        </div>
    @else
        <div class="row business-report-monthly">
            <div class="col-xs-12 business-report-title">
                <h3 class="text-center">{{ $startDate }} 至 {{ $endDate }}</h3>
                <h5 class="text-center">{{ $salesman->name }} - 业务报告</h5>
                <hr>
                <div>
                    <a href="{{ url("business/report/2/export?start_date={$startDate}&end_date={$endDate}") }}" class="btn btn-primary">导出</a>
                </div>
            </div>
            <div class="col-xs-12">
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
                                <td rowspan="{{ (count($visit['display_fee']) ? (count($visit['display_fee']) + 2) : 0) + (isset($visit['mortgage']) ? (count(array_flatten($visit['mortgage']))/3 + 2) : 0) }}">
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
                                    <td rowspan="{{ count(array_flatten($visit['mortgage']))/3 + 2 }}">
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
                                        <td>{{ $mortgage['pieces'] }}</td>
                                        <td colspan="2">{{ $mortgage['num'] }}</td>

                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
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
                                <td>{{ $statistics['return_order_num'] }}</td>
                                <td>{{ $statistics['return_amount'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="6">
                                <b>订货总金额 : {{ $visit['amount'] }}</b> &nbsp;&nbsp;<b>退货总金额
                                    : {{ $visit['return_amount'] }}</b>
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        </div>
    @endif
@stop
