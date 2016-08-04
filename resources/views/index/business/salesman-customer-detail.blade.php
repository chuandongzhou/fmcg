@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '业务管理-业务员管理')
@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    <a href="{{ url('business/salesman-customer') }}">客户管理</a> &rarr;
    客户明细
@stop

@section('right')
    <div class="form-group">
        <form action="{{ url('business/salesman-customer/' . $customer->id) }}">
            <input class="datetimepicker inline-control" placeholder="选择开始时间" name="begin_time"
                   data-format="YYYY-MM-DD"
                   type="text" value="{{ $beginTime }}"> 至
            <input class="datetimepicker inline-control" placeholder="选择结束时间" name="end_time"
                   data-format="YYYY-MM-DD"
                   value="{{ $endTime }}" type="text">
            <button id="submitBtn" class="btn search-by-get" type="submit">查询</button>
            <a id="export"
               href="{{ url('business/salesman-customer/' . $customer->id .'/export?begin_time=' . $beginTime . '&end_time=' . $endTime) }}"
               class="btn btn-primary">导出</a>
        </form>
    </div>
    <div class="row sales-details-panel">
        <div class="col-xs-3">
            <div class="item"><span class="prompt">店铺名称</span> : <span>{{ $customer->name }}</span></div>
            <div class="item"><span class="prompt">业务员</span> : <span>{{ $customer->salesman->name }}</span></div>
            <div class="item"><span class="prompt">订货总订单数</span> : <span>{{ $orders->count() }}</span></div>
            <div class="item"><span class="prompt">退货总订单数</span> : <span>{{ $returnOrders->count() }}</span></div>
        </div>
        <div class="col-xs-3">
            <div class="item"><span class="prompt">联系人</span> : <span>{{ $customer->contact }}</span></div>
            <div class="item"><span class="prompt">拜访次数</span> : <span>{{ $visits->count()   }}</span></div>
            <div class="item"><span class="prompt">订单总金额</span> : <span>{{ $orders->sum('amount') }}</span></div>
            <div class="item"><span class="prompt">退货总金额</span> : <span>{{ $returnOrders->sum('amount') }}</span>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="item"><span class="prompt">联系电话</span> : <span>{{ $customer->contact_information }}</span>
            </div>
            <div class="item"><span class="prompt">最后拜访时间</span> : <span>{{ $visits->max('created_at') }}</span>
            </div>
        </div>
    </div>
    <div class="row sales-details-tabs">
        <div class="col-xs-12">
            <table class="table text-center table-bordered table-middle">
                @if($orders->count())
                    <tr>
                        <td rowspan="{{ ($orders->count()+ 2) + ($mortgageGoods->count() ? ($mortgageGoods->count() + 2)     : 0) }}">
                            陈列费
                        </td>
                        <td colspan="4">现金</td>
                    </tr>
                    <tr>
                        <td colspan="2">现金</td>
                        <td colspan="2">拜访时间</td>
                    </tr>
                    @foreach($orders as $order)
                        <tr>
                            <td colspan="2">{{ $order->display_fee }}</td>
                            <td colspan="2">{{ $order->created_at }}</td>
                        </tr>
                    @endforeach
                @endif
                @if($mortgageGoods->count())
                    <tr>
                        @if(!$orders->count())
                            <td rowspan="{{ $mortgageGoods->count() + 2 }}">陈列费</td>
                        @endif
                        <td colspan="4">货抵</td>
                    </tr>
                    <tr>
                        <td>拜访时间</td>
                        <td>商品名称</td>
                        <td>商品单位</td>
                        <td>数量</td>
                    </tr>
                    @foreach($mortgageGoods as $mortgage)
                        <tr>
                            <td>{{ $mortgage['created_at'] }}</td>
                            <td>{{ $mortgage['name'] }}</td>
                            <td>{{ cons()->valueLang('goods.pieces' , $mortgage['pieces']) }}</td>
                            <td>{{ $mortgage['num'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
        <div class="col-xs-12">
            <table class="table text-center table-bordered table-middle">
                <thead>
                <tr>
                    <th colspan="10" class="text-center">客户销售商品列表</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>商品ID</td>
                    <td>商品名称</td>
                    <td>拜访时间</td>
                    <td>商品库存</td>
                    <td>生产日期</td>
                    <td>商品单价</td>
                    <td>订货数量</td>
                    <td>订货总金额</td>
                    <td>退货数量</td>
                    <td>退货总金额</td>
                </tr>
                @foreach($salesListsData as $goodsId => $salesGoods)
                    @foreach($salesGoods['visit'] as $visit)
                        <tr>
                            @if($visit == head($salesGoods['visit']))
                                <td rowspan="{{ count($salesGoods['visit']) }}">{{ $salesGoods['id'] }}</td>
                                <td rowspan="{{ count($salesGoods['visit']) }}">{{ $salesGoods['name'] }}</td>
                            @endif
                            <td>{{ $visit['time'] }}</td>
                            <td>{{ $visit['stock'] }}</td>
                            <td>{{ $visit['production_date'] }}</td>
                            <td>{{ $visit['order_price'] }}
                                /{{ cons()->valueLang('goods.pieces' , $visit['order_pieces'])}}</td>
                            <td>{{ $visit['order_num']  . cons()->valueLang('goods.pieces' , $visit['order_pieces']) }}</td>
                            <td>{{ $visit['order_amount'] }}</td>
                            <td>{{ $visit['return_num'] }}</td>
                            <td>{{ $visit['return_amount'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @parent
@stop
