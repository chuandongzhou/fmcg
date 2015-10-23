@extends('index.menu-master')
@section('right')
    <div class="col-sm-10 order-detail">
        <div class="row">
            <div class="col-sm-12 order-panel">
                <ul>
                    <li><span class="title-name">订单号 :</span> {{ $order['id'] }}</li>
                    <li><span class="title-name">订单金额 :</span><span class="red">￥{{ $order['price'] }}</span></li>
                    <li><span class="title-name">支付方式 :</span>{{ $order['payment_type'] }}</li>
                    <li><span class="title-name">订单状态 :</span><span class="red">{{ $order['status_name'] }}</span></li>
                    <li><span class="title-name">订单备注 :</span>

                        <p class="remarks-content">{{ $order['remark'] }}</p>
                    </li>
                </ul>
            </div>
            <div class="col-sm-12">
                <div id="stepBar" class="ui-stepBar-wrap">
                    <div class="ui-stepBar">
                        <div class="ui-stepProcess"></div>
                    </div>
                    <div class="ui-stepInfo-wrap">
                        <div class="ui-stepLayout" border="0" cellpadding="0" cellspacing="0">
                            <ul>
                                <li class="ui-stepInfo">
                                    <a class="ui-stepSequence"></a>

                                    <p class="ui-stepName">未发货</p>
                                </li>
                                <li class="ui-stepInfo">
                                    <a class="ui-stepSequence"></a>

                                    <p class="ui-stepName">已发货</p>
                                </li>
                                <li class="ui-stepInfo">
                                    <a class="ui-stepSequence"></a>

                                    <p class="ui-stepName">已付款</p>
                                </li>
                                <li class="ui-stepInfo">
                                    <a class="ui-stepSequence"></a>

                                    <p class="ui-stepName">已完成</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 order-table table-responsive text-center">
                <table class="table table-bordered little-table">
                    <thead>
                    <tr>
                        <td>订单操作</td>
                        <td>操作时间</td>
                        <td>操作人</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>提交订单</td>
                        <td>{{ $order['created_at'] }}</td>
                        <td>{{ $order['user']['user_name'] }}</td>
                    </tr>
                    {{--@if((int)$order['confirmed_at'])--}}
                        {{--<tr>--}}
                            {{--<td>确认订单</td>--}}
                            {{--<td>{{ $order['confirmed_at'] }}</td>--}}
                            {{--<td>{{ $order['shop']['contact_person'] }}</td>--}}
                        {{--</tr>--}}
                    {{--@endif--}}
                    @if((int)$order['send_at'])
                        <tr>
                            <td>
                                <p>订单发货、配送人:{{ $order['delivery_man']['name'] }}</p>

                                <p>联系方式:{{ $order['delivery_man']['phone'] }}</p>
                            </td>
                            <td>{{ $order['send_at'] }}</td>
                            <td>{{ $order['shop']['contact_person'] }}</td>
                        </tr>
                    @endif
                    @if((int)$order['paid_at'])
                        <tr>
                            <td>付款</td>
                            <td>{{ $order['paid_at'] }}</td>
                            <td>{{ $order['user']['user_name'] }}</td>
                        </tr>
                    @endif
                    @if((int)$order['finished_at'])
                        <tr>
                            <td>完成</td>
                            <td>{{ $order['finished_at'] }}</td>
                            <td>{{ $order['shop']['contact_person'] }}</td>
                        </tr>
                    @endif
                    @if($order['is_cancel'])
                        <tr>
                            <td>取消订单</td>
                            <td>{{ $order['cancel_at'] }}</td>
                            <td>{{ $order['cancel_by'] == $order['user']['user_name'] ? $order['user']['user_name'] : $order['shop']['contact_person'] }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12 store-msg">
                <div class="clearfix item">
                    <label class="pull-left title-name">商家信息</label>
                    <ul class="pull-left">
                        <li>
                            <span>商家名称 :</span>
                            <span>{{ $order['shop']['name'] }}</span>
                        </li>
                        <li>
                            <span>联系人 :</span>
                            <span>{{ $order['shop']['contact_person'] }}</span>
                        </li>
                        <li>
                            <span>联系电话 :</span>
                            <span>{{ $order['shop']['contact_info'] }}</span>
                        </li>
                    </ul>
                </div>
                <div class="clearfix item">
                    <label class="pull-left title-name">配送人信息</label>
                    <ul class="pull-left">
                        <li>
                            <span>联系人 :</span>
                            <span>{{ $order['delivery_man']['name'] }}</span>
                        </li>
                        <li>
                            <span>联系电话 :</span>
                            <span>{{ $order['delivery_man']['phone'] }}</span>
                        </li>
                        <li>
                            <span>预计到达 :</span>
                            <span>2015年8月28日 16:45</span>
                        </li>
                    </ul>
                </div>
                <div class="item">
                    <label class="title-name">收货地址</label>
                    <span>{{ $order['shipping_address']['address']['area_name'] . $order['shipping_address']['address']['address'] }}</span>
                </div>
                <div class="table-responsive order-table clearfix item">
                    <label class="pull-left title-name">商品清单</label>
                    <table class=" table table-bordered ">
                        <thead>
                        <tr>
                            <td>商品编号</td>
                            <td>商品图片</td>
                            <td>商品名称</td>
                            <td>商品单价</td>
                            <td>商品数量</td>
                            <td>金额</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order['goods'] as $good)
                            <tr>
                                <td>{{ $good['id'] }}</td>
                                <td><img src="{{ $good['image_url'] }}"></td>
                                <td>{{ $good['name'] }}</td>
                                <td>￥{{ $good['pivot']['price'] }}</td>
                                <td>{{ $good['pivot']['num'] }}</td>
                                <td>￥{{ $good['pivot']['total_price'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-12 text-right bottom-content">
                <p>总额<b class="red">￥{{ $order['price'] }}</b></p>

                <p>
                    @if(!$order['is_cancel'])
                        @if($order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] == cons('order.status.non_send'))
                            <a class="btn btn-danger ajax" data-url="{{ url('order-buy/cancel-sure') }}"
                               data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消</a>
                        @elseif($order['pay_type'] == cons('pay_type.online') && $order['status'] == cons('order.status.send'))
                            <a class="btn btn-danger ajax" data-url="{{ url('order-buy/batch-finish') }}"
                               data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a>
                        @endif
                    @endif
                </p>
            </div>
        </div>
    </div>
@stop
@include('includes.stepBar')