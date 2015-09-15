@extends('index.switch')
@include('includes.timepicker')
@section('container')
    <div class="container my-goods order-report">
        <div class="row">
            @include('index.retailer-left')
            <div class="col-sm-10 content">
                {{--<div class="col-sm-12 title">--}}
                    {{--<a href="#" class="active">待付款2</a>--}}
                    {{--<a href="#">待收货2</a>--}}
                {{--</div>--}}
                <form action="{{ url('order/statistics') }}" method="get">
                    <div class="col-sm-12 enter-item">
                        时间段
                        <input class="enter datetimepicker" name="start_at" placeholder="{{ $start_at or '开始时间' }}" type="text" value="{{ $start_at or '' }}">至
                        <input class="enter datetimepicker" name="end_at" placeholder="{{ $end_at or '开始时间' }}" type="text" value="{{ $end_at or '' }}">
                        <select class="enter" name="pay_type">
                            <option value="">全部方式</option>
                            @foreach($pay_type as $key => $value)
                                <option value="{{ $key }}" {{ $key==$selectedPay ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if(Auth()->user()->type == cons('user.type.wholesaler'))
                            <input type="hidden" name="obj_type" value="{{ $selectedObj }}" />
                        @else
                        <select class="enter" name="obj_type">
                            <option value="">全部订单对象</option>

                            @foreach($obj_type as $key => $value)
                                <option value="{{ $key }}" {{ $key==$selectedObj ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @endif
                        <button class="btn" type="submit" >统计</button>
                        <button class="btn btn-primary">统计导出</button>
                    </div>
                    @if(Auth()->user()->type == cons('user.type.retailer'))
                        <div class="col-sm-12 enter-item">
                            <div class="check-item"><span class="span-checkbox"><i class="fa"></i></span><input class="inp-checkbox" type="checkbox">显示商品</div>
                            <input type="text" class="enter" placeholder="商品名称">
                            <p class="item address" >地区 :
                                <select class="enter">
                                    <option>省</option>
                                </select>
                                <select class="enter">
                                    <option>市</option>
                                </select>
                                <select class="enter">
                                    <option>区</option>
                                </select>
                            </p>
                        </div>
                    @endif
                </form>
                <div class="col-sm-12 table-responsive tables">
                    <table class="table-bordered table">
                        <thead>
                            <tr>
                                <td>订单号</td>
                                <td>收件人</td>
                                <td>支付方式</td>
                                <td>订单状态</td>
                                <td>时间</td>
                                <td>订单金额</td>
                                <td>商品编号</td>
                                <td>商品名称</td>
                                <td>商品单价</td>
                                <td>商品数量</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['data'] as $order)
                                @foreach($order['goods'] as $key => $value)
                                    @if($key)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $value['id'] }}</td>
                                            <td>{{ $value['name'] }}</td>
                                            <td>￥{{ $value['pivot']['price'] }}</td>
                                            <td>{{ $value['pivot']['num'] }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $order['id'] }}</td>
                                            <td>{{ $order['shipping_address']['consigner'] }}</td>
                                            <td>{{ $order['payment_type'] }}</td>
                                            <td>{{ $order['status_name'] }}</td>
                                            <td>{{ $order['created_at'] }}</td>
                                            <td>￥{{ $order['price'] }}</td>
                                            <td>{{ $value['id'] }}</td>
                                            <td>{{ $value['name'] }}</td>
                                            <td>￥{{ $value['pivot']['price'] }}</td>
                                            <td>{{ $value['pivot']['num'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 table-responsive tables">
                    <p class="title-table">订单总计</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>订单数</td>
                                <td>总金额</td>
                                <td>在线支付订单数</td>
                                <td>在线支付订单总金额</td>
                                <td>货到付款订单数</td>
                                <td>货到付款总金额</td>
                                <td>货到付款实收金额</td>
                                <td>货到付款未收金额</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $otherStat['totalNum'] }}</td>
                                <td>{{ $otherStat['totalAmount'] }}</td>
                                <td>{{ $otherStat['onlineNum'] }}</td>
                                <td>{{ $otherStat['onlineAmount'] }}</td>
                                <td>{{ $otherStat['codNum'] }}</td>
                                <td>{{ $otherStat['codAmount'] }}</td>
                                <td>{{ $otherStat['codReceiveAmount'] }}</td>
                                <td>{{ $otherStat['codAmount'] - $otherStat['codReceiveAmount'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 table-responsive tables">
                    <p class="title-table">商品总计</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>商品编号</td>
                                <td>商品名称</td>
                                <td>平均单价</td>
                                <td>商品数量</td>
                                <td>商品支出金额</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otherStat['goods'] as $key => $good )
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $good['name'] }}</td>
                                <td>{{ $good['price']/$good['num'] }}</td>
                                <td>{{ $good['num'] }}</td>
                                <td>{{ $good['price'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection