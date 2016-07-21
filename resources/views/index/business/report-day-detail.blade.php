@extends('index.menu-master')
@include('includes.timepicker')
@include('includes.salesman-customer-map')
@section('subtitle', '业务管理-业务报告详细')

@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    <a href="{{ url('business/report') }}">业务员报告</a> &rarr;
    业务报告明细
@stop

@section('right')
    <div class="row">
        <div class="col-xs-12 business-report-title">
            <h3 class="text-center">{{ $startDate }}</h3>
            <h5 class="text-center">{{ $salesman->name }}-业务报告</h5>
            <hr>
            <div>
                <a class="btn btn-default customer-map" href="javascript:" data-target="#customerAddressMapModal"
                   data-toggle="modal">
                    <i class="fa fa-map-marker"></i> 线路图
                </a>
                <a href="{{ url("business/report/{$salesman->id}/export?start_date={$startDate}&end_date={$endDate}") }}"
                   class="btn btn-primary">导出</a>
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

                        <td colspan="3">
                            {{ $visit['shipping_address_name'] }}
                            <input type="hidden" class="map-data"
                                   data-lng="{{ $visit['lng'] }}"
                                   data-lat="{{ $visit['lat'] }}"
                                   data-number="{{ $visit['number'] }}"
                                   data-name="{{ $visit['customer_name'] }}"
                            >
                        </td>
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
                                <td colspan="3">{{  cons()->valueLang('goods.pieces', $mortgage['pieces'])  }}</td>
                                <td colspan="3">{{ $mortgage['num'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if(isset($visit['statistics']))
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
                    @endif
                </table>
            @endforeach
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
                data['number'] = obj.data('number');
                data['name'] = obj.data('name');
                mapData.push(data);
            });
            return mapData;
        };
    </script>
@stop
