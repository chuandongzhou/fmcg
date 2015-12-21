@extends('index.menu-master')
@section('subtitle' , '订单详情')
@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="{{ url('order-sell') }}"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12">
            <div class="row order-tracking">
                <div class="col-sm-12">
                    <p><label>订单跟踪 :</label></p>

                    <div id="stepBar" class="ui-stepBar-wrap">
                        <div class="ui-stepBar">
                            <div class="ui-stepProcess"></div>
                        </div>
                        <div class="ui-stepInfo-wrap">
                            <div class="ui-stepLayout">
                                <ul>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">未付款</p>
                                    </li>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">已付款</p>
                                    </li>
                                    <li class="ui-stepInfo">
                                        <a class="ui-stepSequence"></a>

                                        <p class="ui-stepName">已发货</p>
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
                        </li>
                        <li>{{ $order->user->shop->name }}</li>
                    </ul>
                    @if((int)$order['paid_at'])
                        <ul class="submit-detail-item">
                            <li>付款</li>
                            <li class="time">
                                <span class="date">{{ $order['paid_at'] }}</span>
                            </li>
                            <li>{{ $order->user->shop->name }}</li>
                        </ul>
                    @endif
                    @if((int)$order['refund_at'])
                        <ul class="submit-detail-item">
                            <li>退款</li>
                            <li class="time">
                                <span class="date">{{ $order['refund_at'] }}</span>
                            </li>
                            <li>{{ $order->user->shop->name }}</li>
                        </ul>
                    @endif
                    @if((int)$order['send_at'])
                        <ul class="submit-detail-item">
                            <li>发货</li>
                            <li class="time">
                                <span class="date">{{ $order['send_at'] }}</span>
                            </li>
                            <li>{{ $order->shop->name }}</li>
                        </ul>
                    @endif
                    @if((int)$order['finished_at'])
                        <ul class="submit-detail-item">
                            <li>完成</li>
                            <li class="time">
                                <span class="date">{{ $order['finished_at'] }}</span>
                            </li>
                            <li>{{ $order->user->shop->name }}</li>
                        </ul>
                    @endif
                    @if((int)$order['is_cancel'])
                        <ul class="submit-detail-item">
                            <li>取消订单</li>
                            <li class="time">
                                <span class="date">{{ $order['cancel_at'] }}</span>
                            </li>
                            <li>{{ $order['cancel_by'] == $order->user->id ? $order->user->shop->name : $order->shop->name }}</li>
                        </ul>
                    @endif
                </div>
            </div>
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <ul class="pull-left order-information">
                        <li class="title">订单信息</li>
                        <li>
                            <span class="title-info-name">订单号 :</span> {{ $order['id'] }}
                        </li>
                        <li>
                            <span class="title-info-name">订单金额 : </span><span class="red">￥{{ $order['price'] }}</span>
                        </li>
                        <li>
                            <span class="title-info-name">支付方式 : </span>{{ $order['payment_type'] }}
                        </li>
                        <li>
                            <span class="title-info-name">订单状态 : </span>
                            <span class="red">{{ $order['status_name'] }}</span>
                        </li>
                        <li>
                            <span class="title-info-name">订单备注 :</span>

                            <p class="remarks-content">{{ $order['remark'] }}</p>
                        </li>
                    </ul>
                    <div class="pull-right">
                        @if(!$order['is_cancel'])
                            @if($order['can_cancel'])
                                <button class="btn btn-cancel ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                                        data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消
                                </button>
                            @endif
                            @if($order['can_send'])
                                <a class="btn btn-warning send-goods" data-target="#sendModal"
                                   data-toggle="modal" data-data="{{ $order['id'] }}">发货</a>
                            @endif
                            @if($order['can_export'])
                                <a target="_blank" class="btn btn-success"
                                   href="{{ url('order-sell/export?order_id='.$order['id']) }}">导出</a>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-sm-12 receiving-information">
                    <ul>
                        <li class="title">收货人信息</li>
                        <li><span class="title-info-name">终端商名称 : </span>{{ $order->user->shop->name }}</li>
                        <li><span class="title-info-name">联系人 : </span>{{ $order['shippingAddress']['consigner'] }}</li>
                        <li><span class="title-info-name">联系电话 : </span>{{ $order['shippingAddress']['phone'] }}</li>
                        <li>
                            <span class="title-info-name">联系地址 : </span>{{ $order['shippingAddress']['address']['area_name'] . $order['shipping_address']['address']['address'] }}
                        </li>
                    </ul>
                </div>
                @if((int)$order['send_at'])
                    <div class="col-sm-12 receiving-information">
                        <ul>
                            <li class="title">配送人信息</li>
                            <li><span class="title-info-name">联系人 : </span>{{ $order['deliveryMan']['name'] }}
                            </li>
                            <li><span class="title-info-name">联系电话 : </span>{{ $order['deliveryMan']['phone'] }}</li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="row table-row">
                <div class="col-sm-12 table-responsive table-col">
                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th>商品编号</th>
                            <th>商品图片</th>
                            <th>商品名称</th>
                            <th>商品价格</th>
                            <th>商品数量</th>
                            <th>金额</th>
                            @if($order['status']<cons('order.status.send') && $order['is_cancel'] == cons('order.is_cancel.off'))
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order['goods'] as $goods)
                            <tr>
                                <td>{{ $goods['id'] }}</td>
                                <td><img class="store-img" src={{ $goods['image_url'] }} /></td>
                                <td><a href="{{ url('my-goods/'. $goods['id']) }}">{{ $goods['name'] }}</a></td>
                                <td>{{ $goods['pivot']['price'] }}
                                    / {{ cons()->valueLang('goods.pieces' , $goods->{'pieces_' . $order->user->type_name})  }}</td>
                                <td>{{ $goods['pivot']['num'] }}</td>
                                <td>{{ $goods['pivot']['price'] * $goods['pivot']['num'] }}</td>
                                @if($order['status']<cons('order.status.send') && $order['is_cancel'] == cons('order.is_cancel.off'))
                                    <td><a class="change-price" href="javascript:void(0)" data-target="#changePrice"
                                           data-toggle="modal" data-data="{{ $order['id'] }}"
                                           data-pivot="{{  $goods['pivot']['id'] }}">修改</a></td>
                                @endif
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
    </div>
    @include('includes.order-select-delivery_man')
    @include('includes.order-change-price')
@stop
@include('includes/stepBar')
@section('js')
    @parent
    <script>
        $(function () {
            sendGoodsByDetailPage();
            changePriceByDetailPage();
        })
    </script>
@stop