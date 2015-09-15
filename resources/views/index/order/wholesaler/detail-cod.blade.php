@extends('index.switch')
@section('right')
    <div class="col-sm-10 order-detail">

        <div class="row order-tracking">
            <div class="col-sm-12">
                <p><label>订单跟踪 :</label></p>

                <div id="stepBar" class="ui-stepBar-wrap">
                    <div class="ui-stepBar">
                        <div class="ui-stepProcess"></div>
                    </div>
                    <div class="ui-stepInfo-wrap">
                        <div class="ui-stepLayout" border="0" cellpadding="0" cellspacing="0">
                            <ul>
                                <li class="ui-stepInfo">
                                    <a class="ui-stepSequence"></a>

                                    <p class="ui-stepName">未确认</p>
                                </li>
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
            <div class="col-sm-12 orders-submit-detail">
                <ul class="submit-detail-item">
                    <li>订单操作</li>
                    <li>操作时间</li>
                    <li>操作人</li>
                </ul>
                <ul class="submit-detail-item">
                    <li>提交订单</li>
                    <li class="time">
                        <span class="date">{{ $order['created_at'] }}</span>
                        {{--<span class="clock">11:20</span>--}}
                    </li>
                    <li>{{ $order['user']['user_name'] }}</li>
                </ul>
                @if((int)$order['confirmed_at'])
                    <ul class="submit-detail-item">
                        <li>确认订单</li>
                        <li class="time">
                            <span class="date">{{ $order['confirmed_at'] }}</span>
                            {{--<span class="clock">11:20</span>--}}
                        </li>
                        <li>{{ $order['shop']['contact_person'] }}</li>
                    </ul>
                @endif
                @if($order['is_cancel'])
                    <ul class="submit-detail-item">
                        <li>取消订单</li>
                        <li class="time">
                            <span class="date">{{ $order['cancel_at'] }}</span>
                            {{--<span class="clock">11:20</span>--}}
                        </li>
                        <li>{{ $order['cancel_by'] == $order['user']['user_name'] ? $order['user']['user_name'] : $order['shop']['contact_person'] }}</li>
                    </ul>
                @endif
                @if((int)$order['send_at'])
                    <ul class="submit-detail-item">
                        <li>发货</li>
                        <li class="time">
                            <span class="date">{{ $order['send_at'] }}</span>
                            {{--<span class="clock">11:20</span>--}}
                        </li>
                        <li>{{ $order['shop']['contact_person'] }}</li>
                    </ul>
                @endif
                @if((int)$order['paid_at'])
                    <ul class="submit-detail-item">
                        <li>付款</li>
                        <li class="time">
                            <span class="date">{{ $order['paid_at'] }}</span>
                            {{--<span class="clock">11:20</span>--}}
                        </li>
                        <li>{{ $order['user']['user_name'] }}</li>
                    </ul>
                @endif
                @if((int)$order['finished_at'])
                    <ul class="submit-detail-item">
                        <li>完成</li>
                        <li class="time">
                            <span class="date">{{ $order['finished_at'] }}</span>
                            {{--<span class="clock">11:20</span>--}}
                        </li>
                        <li>{{ $order['user']['user_name'] }}</li>
                    </ul>
                @endif
            </div>
        </div>
        <div class="row order-receipt">
            <div class="col-sm-12">
                <ul class="pull-left order-information">
                    <li class="title">订单信息</li>
                    <li>订单号 : {{ $order['id'] }}</li>
                    <li>订单金额 : <span class="red">￥{{ $order['price'] }}</span></li>
                    <li>支付方式 : {{ $order['payment_type'] }}</li>
                    <li>订单状态 : <span class="red">{{ $order['status_name'] }}</span></li>
                    <li>订单备注 :
                        <p class="remarks-content">{{ $order['remark'] }}</p>
                    </li>
                </ul>
                <div class="pull-right">
                    @if(Auth()->user()->id == $order['user']['id'])
                        {{--买家显示按钮，如果订单被取消则只显示导出功能按钮--}}
                        <div class="pull-right">
                            @if(!$order['is_cancel'])
                                @if($order['pay_status'] == cons('pay_type.non_payment') && $order['status'] == cons('order.status.non_send'))
                                    <button class="btn btn-cancel ajax" data-url="{{ url('order-sell/cancel-sure') }}"
                                            data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消</button>
                                @endif
                                @if($order['pay_status'] == cons('pay_type.payment_success') && $order['status'] == cons('order.status.send') )
                                    <button class="btn btn-primary ajax" data-url="{{ url('order-buy/batch-finish') }}"
                                            data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>已收货</button>
                                @endif
                                @if($order['pay_status'] == cons('pay_type.non_payment') && $order['status'] == cons('order.status.non_send'))
                                    {{--跳转支付页面--}}
                                    <button class="btn btn-danger">付款</button>
                                @endif
                            @endif
                        </div>
                    @else
                        {{--卖家显示按钮--}}
                        <div class="pull-right">
                            @if(!$order['is_cancel'])
                                @if($order['status'] == cons('order.status.non_sure'))
                                    <button class="btn btn-danger ajax" data-method = 'put' data-url="{{ url('order-sell/batch-sure') }}"
                                            data-data='{"order_id":{{ $order['id'] }}}'>确认</button>
                                @endif
                                @if($order['pay_status'] == cons('pay_type.non_payment') && $order['status'] == cons('order.status.non_send'))
                                    <button class="btn btn-cancel ajax" data-url="{{ url('order-sell/cancel-sure') }}"
                                            data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消</button>
                                @endif
                                @if($order['pay_status'] == cons('pay_type.payment_success') && $order['status'] == cons('order.status.non_send') )
                                    <button class="btn btn-primary ajax" data-method = 'put' data-url="{{ url('order-sell/batch-send') }}"
                                            data-data='{"order_id":{{ $order['id'] }}}'>发货</button>
                                        <button class="btn btn-success">导出</button>
                                @endif
                            @endif

                        </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-12 receiving-information">
                <ul>
                    <li class="title">收货人信息</li>
                    <li>终端商名称 : {{ $order['user']['user_name'] }}<li>
                    <li>联系人 : {{ $order['shipping_address']['consigner'] }}</li>
                    <li>联系电话 : {{ $order['shipping_address']['phone'] }}</li>
                    <li>联系地址 : {{ $order['shipping_address']['address'] }}</li>
                </ul>
            </div>
        </div>
        <div class="row table-row">
            <div class="col-sm-12 table-responsive table-col">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>商品编号</th>
                            <th>商品图片</th>
                            <th>商品名称</th>
                            <th>商品价格</th>
                            <th>商品数量</th>
                            <th>金额</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order['goods'] as $item)
                            <tr>
                                <td>{{ $item['id'] }}</td>
                                <td><img src={{ $item['image_url'] }} /></td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['pivot']['price'] }}</td>
                                <td>{{ $item['pivot']['num'] }}</td>
                                <td>{{ $item['pivot']['total_price'] }}</td>
                                <td>修改</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12 text-right">
                总额 : <b class="red">￥{{ $order['price'] }}</b>
            </div>
        </div>
    </div>
@endsection
@include('includes.stepBar')
