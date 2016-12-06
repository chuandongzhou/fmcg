@extends('index.menu-master')
@section('subtitle' , '订单详情')
@include('includes.shipping-address-map')
@section('top-title')
    <a href="{{ url('order-buy') }}">进货管理</a> >
    <span class="second-level">订单详情</span>
@stop
@include('includes.pay')
@include('includes.stepBar')
@include('includes.order-refund')
@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back btn btn-border-blue" href="javascript:history.back()"><i class="iconfont icon-fanhui"></i> 返回</a>
        </div>
        <div class="col-sm-12">
            @if($order->pay_type!=cons('pay_type.pick_up'))
                <div class="row order-tracking">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">订单跟踪</h3>
                            </div>
                            <div class="panel-container">
                                <div id="stepBar" class="ui-stepBar-wrap">
                                    <div class="ui-stepBar">
                                        <div class="ui-stepProcess"></div>
                                    </div>
                                    <div class="ui-stepInfo-wrap">
                                        <div class="ui-stepLayout" border="0" cellpadding="0" cellspacing="0">
                                            <ul>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?'未付款':'未发货' }}</div>

                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?'已付款':'已发货' }}</div>
                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?'已发货':'已付款' }}</div>
                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">已完成</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">订单信息</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center">
                                <tr>
                                    <th>订单号</th>
                                    <th>订单金额</th>
                                    <th>陈列费/优惠券</th>
                                    <th>应付金额</th>
                                    <th>支付方式</th>
                                    <th>订单状态</th>
                                    <th>备注</th>
                                    <td rowspan="2">
                                        @if(!$order['is_cancel'])
                                            @if($order->pay_type==cons('pay_type.pick_up'))
                                                @if($order['can_cancel'])
                                                    <a class="btn btn-danger ajax"
                                                       data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                       data-method="put"
                                                       data-data='{"order_id":{{ $order['id'] }}}'>取消</a>
                                                @endif
                                            @else
                                                @if ($order->can_refund)
                                                    <a class="btn btn-danger refund" data-target="#refund"
                                                       data-toggle="modal"
                                                       data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                        退款
                                                    </a>
                                                @elseif($order['can_cancel'])
                                                    <a class="btn btn-red ajax"
                                                       data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                       data-method="put"
                                                       data-data='{"order_id":{{ $order['id'] }}}'>取消</a>
                                                @endif
                                                @if($order['can_payment'])
                                                    <a href="javascript:" data-target="#payModal" data-toggle="modal"
                                                       class="btn btn-success" data-id="{{ $order->id }}"
                                                       data-price="{{ $order->after_rebates_price }}">去付款</a>
                                                @elseif($order['can_confirm_arrived'])
                                                    <a class="btn btn-danger ajax"
                                                       data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                                       data-method="put"
                                                       data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a>
                                                @endif
                                            @endif

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    <td>￥{{ $order['price'] }}</td>
                                    <td>
                                        {{ $order->coupon_id?' ￥'.bcsub($order->price, $order->after_rebates_price, 2):($order->display_fee > 0?$order->display_fee:'') }}</td>
                                    <td><span class="orange">¥{{ $order->after_rebates_price }}</span></td>
                                    <td>{{ $order['payment_type'] }}
                                        {{ $order->pay_type==cons('pay_type.cod')?'('.$order->pay_way_lang.')':'' }}
                                    </td>
                                    <td><span class="orange">{{ $order['status_name'] }}</span></td>
                                    <td width="15%">{{ $order['remark'] }}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{  $order->pay_type==cons('pay_type.pick_up')?'提货人信息':'商家信息' }}</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center">
                                <tr>
                                    <th>商家信息</th>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>{{  $order->pay_type!=cons('pay_type.pick_up')?'收货地址':'提货地址' }}</th>
                                </tr>
                                <tr>
                                    <td><p>{{ $order['shop']['name'] }}</p>
                                        <p class="prop-item">
                                            <a href="javascript:"
                                               onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$order['shop']['id']) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                               class="contact"><span class="iconfont icon-kefu"></span> 联系客户</a>
                                        </p>
                                    </td>
                                    <td>{{ $order['shop']['contact_person'] }}</td>
                                    <td>{{ $order['shop']['contact_info'] }}</td>
                                    @if( $order->pay_type!=cons('pay_type.pick_up'))
                                        <td>
                                            <p> {{  isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}</p>
                                        </td>
                                     @else
                                        <td>
                                            <p> {{  $order->shop ? $order->shop->address : '' }}</p>
                                            <p class="prop-item">
                                                <a href="javascript:" data-target="#shippingAddressMapModal" data-toggle="modal"
                                                   data-x-lng="{{ $order->shop ?  $order->shop->x_lng : 0 }}"
                                                   data-y-lat="{{ $order->shop ?  $order->shop->y_lat : 0}}"
                                                   data-address="{{ $order->shop ? $order->shop->address : '' }}"
                                                   data-consigner="{{ $order->shop ? $order->shop->contact_person : ''  }}"
                                                   data-phone= {{  $order->shop ? $order->shop->contact_info : '' }}
                                                >
                                                    <i class="iconfont icon-chakanditu"></i> 查看地图
                                                </a>
                                            </p>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @if($order->pay_type!=cons('pay_type.pick_up') && (int)$order['send_at'])
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">配送人信息</h3>
                            </div>

                            <div class="panel-container">
                                @foreach($order->deliveryMan as $deliveryMan)
                                    <ul class="contacts clearfix">
                                        <li class="label-prompt">联系人 :</li>
                                        <li>{{ $deliveryMan->name }}</li>
                                        <li>{{ $deliveryMan->phone }}</li>
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if(!$order->orderChangeRecode->isEmpty())
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">订单修改记录</h3>
                            </div>
                            <div class="panel-container table-responsive">
                                <table class="table table-bordered table-center">
                                    <tr>
                                        <th>时间</th>
                                        <th>修改人</th>
                                        <th>修改内容</th>
                                    </tr>
                                    @foreach($order->orderChangeRecode->reverse() as $orderChangeRecode)
                                        <tr>
                                            <td>{{ $orderChangeRecode->created_at }}</td>
                                            <td>
                                                <b>{{ $orderChangeRecode->user_id == $order->shop->user->id ? $order->shop->name : $order->deliveryMan()->find($orderChangeRecode->user_id)->pluck('name') }}</b>
                                            </td>
                                            <td>{{ $orderChangeRecode->content }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">订单商品</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center">

                                <tr>
                                    <th>商品编号</th>
                                    <th>商品图片</th>
                                    <th>商品名称</th>
                                    <th>商品价格</th>
                                    <th>商品数量</th>
                                    <th>金额</th>
                                </tr>
                                @foreach($orderGoods as $goods)
                                    <tr>
                                        <td>{{ $goods['id'] }}</td>
                                        <td><img class="store-img"
                                                 src={{ $goods['image_url'] }}>
                                        </td>
                                        <td width="30%">
                                            <div class="product-panel">
                                                <a class="product-name"
                                                   href="{{ url('my-goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                                {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>
                                        </td>
                                        <td>{{ $goods['pivot']['price'] }}
                                            /{{ cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}</td>
                                        <td>{{ $goods['pivot']['num'] }}</td>
                                        <td>{{ $goods['pivot']['total_price'] }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" class="pay-item">
                                        总额 : <span class="red">￥{{ $order->price }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($order->pay_type==cons('pay_type.cod') && !$mortgageGoods->isEmpty())
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">抵费商品</h3>
                            </div>
                            <div class="panel-container table-responsive">
                                <table class="table table-bordered table-center">
                                    <tr>
                                        <th>商品编号</th>
                                        <th>商品图片</th>
                                        <th>商品名称</th>
                                        <th>商品数量</th>
                                    </tr>
                                    @foreach($mortgageGoods as $goods)
                                        <tr>
                                            <td>{{ $goods['id'] }}</td>
                                            <td><img class="store-img" src={{ $goods['image_url'] }} /></td>
                                            <td>
                                                <div class="product-panel">
                                                    <a class="product-name"
                                                       href="{{ url('my-goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                                    {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                                </div>
                                            </td>
                                            <td>{{ $goods['pivot']['num'] }}</td>
                                        </tr>

                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                @if($order->pay_type!=cons('pay_type.pick_up'))
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">订单记录</h3>
                            </div>
                            <div class="panel-container table-responsive">
                                <table class="table table-bordered table-center">
                                    <tr>
                                        <th>订单操作</th>
                                        <th>操作时间</th>
                                        <th>操作人</th>
                                    </tr>

                                    <tr>
                                        <td>提交订单</td>
                                        <td>{{ $order['created_at'] }}</td>
                                        <td>{{ $order->user_shop_name }}</td>
                                    </tr>
                                    @if($order->pay_type==cons('pay_type.cod'))
                                        @if((int)$order['send_at'])
                                            <tr>
                                                <td>
                                                    订单发货
                                                </td>
                                                <td>{{ $order['send_at'] }}</td>
                                                <td>{{ $order->shop->name }}</td>
                                            </tr>
                                        @endif
                                        @if((int)$order['paid_at'])
                                            <tr>
                                                <td>付款</td>
                                                <td>{{ $order['paid_at'] }}</td>
                                                <td>{{ $order->user_shop_name }}</td>
                                            </tr>
                                        @endif
                                    @else
                                        @if((int)$order['paid_at'])
                                            <tr>
                                                <td>付款</td>
                                                <td>{{ $order['paid_at'] }}</td>
                                                <td>{{ $order->user_shop_name }}</td>
                                            </tr>
                                        @endif
                                        @if($order->pay_status == cons('order.pay_status.refund')  || $order->pay_status == cons('order.pay_status.refund_success'))
                                            <tr>
                                                <td>申请退款</td>
                                                <td>{{ $order->orderRefund->created_at }}</td>
                                                <td>{{ $order->user_shop_name }}</td>
                                            </tr>
                                        @endif
                                        @if($order->pay_status == cons('order.pay_status.refund_success'))
                                            <tr>
                                                <td>退款成功</td>
                                                <td>{{ $order->refund_at }}</td>
                                                <td>{{ $order->shop->name }}</td>
                                            </tr>
                                        @endif
                                        @if((int)$order['send_at'])
                                            <tr>
                                                <td>
                                                    订单发货
                                                </td>
                                                <td>{{ $order['send_at'] }}</td>
                                                <td>{{ $order->shop->name }}</td>
                                            </tr>
                                        @endif

                                    @endif
                                    @if((int)$order['finished_at'])
                                        <tr>
                                            <td>已完成</td>
                                            <td>{{ $order['finished_at'] }}</td>
                                            <td>{{ $order->user_shop_name }}</td>
                                        </tr>
                                    @endif
                                    @if($order['is_cancel'])
                                        <tr>
                                            <td>取消订单</td>
                                            <td>{{ $order['cancel_at'] }}</td>
                                            <td>{{ $order['cancel_by'] == $order->user->id ? $order->user->shop_name : $order->shop->name }}</td>
                                        </tr>
                                    @endif

                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop
