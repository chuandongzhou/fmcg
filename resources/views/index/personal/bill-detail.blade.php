@extends('index.manage-master')
@section('subtitle', '月对账单')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal') }}">月对账单</a> >
                    <span class="second-level">账单详情</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input placeholder="输入日期" class="enter control datetimepicker" data-format="YYYY-MM" type="text"
                               name="time"
                               value="{{$time ?? \Carbon\Carbon::now()->format('Y-m')}}">
                        <button type="button" class=" btn btn-blue-lighter search-by-get control ">查询</button>
                        <a href="{{url('personal/bill/'.$shop->id.'?time='.($time ?? '').'&act=export')}}"
                           class=" btn btn-border-blue control ">打印</a>
                    </form>
                </div>
                <div class="col-sm-12 statement-item">
                    <div class="col-sm-12 statement-item">
                        <h3 class="text-center title">{{$shop -> name or null }} —— 月对账单</h3>
                        <div class="row list-wrap">
                            <div class="col-sm-5 item-col">
                                <label>商户名称 : </label>
                                {{$shop -> name }}
                            </div>
                            <div class="col-sm-7 item-col">
                                <label>对账时间 : {{$bill['time']['start_at']->toDateString()}}
                                    — {{$bill['time']['end_at']->toDateString()}}</label>

                            </div>
                            <div class="col-sm-12 item-col">
                                <label>商户地址 : {{$shop->shopAddress->area_name ?? ''}}</label>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 statement-table-wrap">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">促销对账单</h3>
                        </div>
                        <div class="panel-container table-responsive padding-clear">
                            <table class="table table-bordered table-center ">
                                <thead>
                                <tr>
                                    <th>时间</th>
                                    <th>促销名称</th>
                                    <th colspan="2">返利项</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td rowspan="3">2016-12-12 10:23:56</td>
                                    <td rowspan="3">12月导购经销商买送</td>
                                    <td>德芙巧丝轻柔夹心威化巧克力202.5g*4盒奥地利进口零食春季出游随身</td>
                                    <td>10个</td>
                                </tr>
                                <tr>
                                    <td>德芙巧丝轻柔夹心威化巧克力202.5g*4盒奥地利进口零食春季出游随身</td>
                                    <td>10个</td>
                                </tr>
                                <tr>
                                    <td>德芙巧丝轻柔夹心威化巧克力202.5g*4盒奥地利进口零食春季出游随身</td>
                                    <td>10个</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">陈列费对账单</h3>
                        </div>
                        <div class="panel-container table-responsive padding-clear">
                            <table class="table table-bordered table-center ">
                                <thead>
                                <tr>
                                    <th>时间</th>
                                    <th>订单编号</th>
                                    <th>月份</th>
                                    <th colspan="2">陈列实发项</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bill['businessOrders'] as $order)
                                    @if($order->mortgageGoods->count())
                                        @foreach($order->mortgageGoods as $mortgageGoods)
                                            <tr>
                                                @if($mortgageGoods == $order->mortgageGoods->first())
                                                    <td rowspan="{{$order->mortgageGoods->count()}}">{{$order->created_at}}
                                                    </td>
                                                    <td rowspan="{{$order->mortgageGoods->count()}}">{{$order->id}}</td>
                                                @endif
                                                <td>{{$mortgageGoods->pivot->month ?? ''}}</td>
                                                <td>{{$mortgageGoods->goods_name ?? ''}}</td>
                                                <td>{{intval($mortgageGoods->pivot->used)}}{{$mortgageGoods->pieces_name}}</td>
                                            </tr>
                                        @endforeach
                                    @elseif($order->displayFees->count())
                                        @foreach($order->displayFees as $displayFees)
                                            <tr>
                                                @if($displayFees == $order->displayFees->first())
                                                    <td rowspan="{{$order->displayFees->count()}}">{{$order->created_at}}</td>
                                                    <td rowspan="{{$order->displayFees ->count()}}">{{$order->id}}</td>
                                                @endif
                                                <td>{{$displayFees->month ?? ''}}</td>
                                                <td>现金</td>
                                                <td>￥{{$displayFees->used}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">订单对账单</h3>
                        </div>
                        <div class="panel-container table-responsive padding-clear">
                            <table class="table table-bordered table-center ">
                                <thead>
                                <tr>
                                    <th>时间</th>
                                    <th>订单编号</th>
                                    <th>商品名称</th>
                                    <th>数量</th>
                                    <th>金额</th>
                                    <th>优惠券</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bill['allOrders'] as $order)
                                    @if($order->orderGoods->count())
                                        @foreach($order->orderGoods as $orderGoods)
                                            <tr>
                                                @if($orderGoods == $order->orderGoods->first())
                                                    <td rowspan="{{$order->orderGoods->count()}}">{{$order->created_at}}</td>
                                                    <td rowspan="{{$order->orderGoods->count()}}">{{$order->id}}</td>
                                                @endif
                                                <td>{{$orderGoods->goods->name ?? ''}}</td>
                                                <td>{{$orderGoods->num ?? ''}} {{$orderGoods->pivot->pieces_name ?? ''}}</td>
                                                <td>{{$orderGoods->total_price ?? ''}}</td>
                                                @if($orderGoods == $order->orderGoods->first())
                                                    <td rowspan="{{$order->orderGoods->count()}}">{{$order->how_much_discount}}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right">
                                        <div class="money-item">
                                            <label>总计金额：</label>{{sprintf("%.2f",$bill['allOrders']->sum('price'))}}
                                        </div>
                                        <div class="money-item">
                                            <label>优惠总额：</label>{{sprintf("%.2f",$bill['allOrders']->sum('how_much_discount'))}}
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">收支明细</h3>
                        </div>
                        <div class="panel-container table-responsive padding-clear">
                            <table class="table table-bordered table-center ">
                                <thead>
                                <tr>
                                    <th>时间</th>
                                    <th>支付平台</th>
                                    <th>交易号</th>
                                    <th>手续费</th>
                                    <th>收支金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bill['paymentDetails'] as $paymentDetail)
                                    <tr>
                                        <td>{{$paymentDetail->finished_at}}</td>
                                        <td>{{cons()->valueLang('trade.pay_type',$paymentDetail->type)}}</td>
                                        <td>{{$paymentDetail->trade_no ?? ''}}</td>
                                        <td>{{$paymentDetail->target_fee ?? '0'}}</td>
                                        <td>¥ {{$paymentDetail->amount}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right">
                                        <div class="money-item">
                                            <label>总计收支金额：</label> {{number_format($bill['paymentDetails']->sum('amount'),2)}}
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 statement-item">
                    <div class="row list-wrap">
                        <div class="col-sm-5 item-col">
                            <label>公司盖章 : </label>
                        </div>
                        <div class="col-sm-7 item-col">
                            <label>客户签字盖章 : </label>
                        </div>
                        <div class="col-sm-5 item-col">
                            <label>日期 : </label>
                        </div>
                        <div class="col-sm-5 item-col">
                            <label>日期 : </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 bottom-note">
                    如果贵方对对账单中数据有疑问，请提供贵明细，以便我们尽快核对您的账目
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script>
        formSubmitByGet();
    </script>
@stop
