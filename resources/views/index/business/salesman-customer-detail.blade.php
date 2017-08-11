@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '业务管理-客户管理-客户明细')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <a href="{{ url('business/salesman-customer') }}">客户管理</a> >
                    <span class="second-level">客户明细</span>
                </div>
            </div>
            <div class="row sales-details-panel">
                <div class="col-sm-12 form-group salesman-controls">
                    <form action="{{ url('business/salesman-customer/' . $customer->id) }}">
                        <input class="datetimepicker inline-control control" placeholder="选择开始时间" name="begin_time"
                               data-format="YYYY-MM-DD"
                               type="text" value="{{ $beginTime }}"> 至
                        <input class="datetimepicker inline-control control" placeholder="选择结束时间" name="end_time"
                               data-format="YYYY-MM-DD"
                               value="{{ $endTime }}" type="text">
                        <button id="submitBtn" class="btn btn-blue-lighter search-by-get" type="submit">查询</button>
                        <a id="export"
                           href="{{ url('business/salesman-customer/' . $customer->id .'/export?begin_time=' . $beginTime . '&end_time=' . $endTime) }}"
                           class="btn btn-border-blue">导出</a>
                    </form>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <b>{{ $customer->name }}- 客户明细</b>
                                <span>{{ $customer->contact }}   {{ $customer->contact_information }}</span>
                            </h3>
                        </div>
                        <div class="panel-container">
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <th>业务员</th>
                                    <th>拜访次数</th>
                                    <th>最后拜访时间</th>
                                    <th>订货总订单数</th>
                                    <th>订货总金额</th>
                                    <th>退货总订单数</th>
                                    <th>退货总金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ $customer->salesman->name }}</td>
                                    <td>{{ $visits->count()   }}</td>
                                    <td>{{ $visits->max('created_at') }}</td>
                                    <td>{{ $orders->count() }}</td>
                                    <td class="red">{{ $orders->sum('amount') }}</td>
                                    <td>{{ $returnOrders->count() }}</td>
                                    <td class="red">{{ $returnOrders->sum('amount') }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-center margin-clear">
                                @if($mortgageGoods->count())
                                    <thead>
                                    <tr>
                                        <th colspan="5" class=" title">陈列费（货抵）</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>有效时间</th>
                                        <th>拜访时间</th>
                                        <th>商品名称</th>
                                        <th>商品单位</th>
                                        <th>数量</th>
                                    </tr>
                                    @foreach($mortgageGoods as $createdAt =>$mortgages)
                                        @foreach($mortgages as $mortgage)
                                            <tr>
                                                <td>{{ $mortgage['month'] }}</td>
                                                @if($mortgage == $mortgages->first())
                                                    <td rowspan="{{ $mortgages->count() }}">{{ $createdAt }}</td>
                                                @endif
                                                <td>{{ $mortgage['name'] }}</td>
                                                <td>{{ cons()->valueLang('goods.pieces' , $mortgage['pieces']) }}</td>
                                                <td>{{ $mortgage['num'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                @elseif($displayFees->count())
                                    <thead>
                                    <tr>
                                        <th colspan="4" class=" title">陈列费（现金）</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>月份</th>
                                        <th>现金</th>
                                        <td colspan="2">拜访时间</td>
                                    </tr>
                                    @foreach($displayFees as $displayFee)
                                        <tr>
                                            <td>{{ $displayFee['month'] }}</td>
                                            <td>{{ $displayFee['used'] }}</td>
                                            <td colspan="2">{{ $displayFee['time'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                @endif
                            </table>
                            <table class="table table-bordered table-center">
                                <thead>
                                <tr>
                                    <th colspan="10" class=" title">销售统计</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>商品ID</th>
                                    <th>商品名称</th>
                                    <th>拜访时间</th>
                                    <th>商品库存</th>
                                    <th>生产日期</th>
                                    <th>商品单价</th>
                                    <th>订货数量</th>
                                    <th>订货总金额</th>
                                    <th>退货数量</th>

                                    <th>退货总金额(元)</th>
                                </tr>
                                @foreach($salesListsData as $goodsId => $salesGoods)
                                    @foreach($salesGoods['visit'] as $visitId =>$visit)
                                        <tr>
                                            @if($visit == head($salesGoods['visit']))
                                                <td rowspan="{{ count($salesGoods['visit']) }}">{{ $salesGoods['id'] }}</td>
                                                <td rowspan="{{ count($salesGoods['visit']) }}"
                                                    width="20%">{{ $salesGoods['name'] }}</td>
                                            @endif
                                            <td width="15%">
                                                {{ $visit['time'] }} ({{ $visitId ? '拜访' : '自主' }})
                                            </td>
                                            <td>{{ $visit['stock'] }}</td>
                                            <td>{{ $visit['production_date']==0?'--':$visit['production_date'] }}</td>
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
                            <table class="table table-bordered table-center">
                                <thead>
                                <tr>
                                    <th colspan="10" class=" title">赠品统计</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>商品ID</th>
                                    <th>商品名称</th>
                                    <th>订单ID</th>
                                    <th>下单时间</th>
                                    <th>数量</th>
                                </tr>
                                @foreach($gifts as $goods_id => $gift)
                                    @foreach($gift['describe'] as $key => $describe)
                                        <tr>
                                            @if($key == 0)
                                                <td rowspan="{{count($gift['describe'])}}">{{$goods_id}}</td>
                                                <td rowspan="{{count($gift['describe'])}}">{{$gift['name']}}</td>
                                            @endif
                                            <td>{{$describe['order_id']}}</td>
                                            <td>{{$describe['time']}}</td>
                                            <td>{{$describe['num'] . cons()->valueLang('goods.pieces',$describe['pieces'])}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
