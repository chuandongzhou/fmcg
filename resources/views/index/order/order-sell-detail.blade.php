@extends('index.menu-master')
@section('subtitle' , '订单详情')
@include('includes.shipping-address-map')
@include('includes.order-change-price')
@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> >
    <span class="second-level">订单详情</span>
@stop
@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back btn btn-border-blue" href="{{ $backUrl }}"><i class="iconfont icon-fanhui"></i>
                返回</a>
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
                                                    <div class="ui-stepName ui-stepTime">{{ $order->created_at->format('Y-m-d H:i') }}</div>

                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?'已付款':'已发货' }}</div>
                                                    <div class="ui-stepName">{{$order->pay_type==cons('pay_type.online')?($order->pay_status>cons('order.pay_status.non_payment')?(new Carbon\Carbon($order->paid_at))->format('Y-m-d H:i'):''):($order->status>=cons('order.status.send') && $order->status<cons('order.status.invalid')?(new Carbon\Carbon($order->send_at))->format('Y-m-d H:i'):'') }}</div>

                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?'已发货':'已付款' }}</div>
                                                    <div class="ui-stepName">{{ $order->pay_type==cons('pay_type.online')?($order->status>=cons('order.status.send') && $order->status<cons('order.status.invalid')?(new Carbon\Carbon($order->send_at))->format('Y-m-d H:i'):''):($order->pay_status>cons('order.pay_status.non_payment')?(new Carbon\Carbon($order->paid_at))->format('Y-m-d H:i'):'') }}</div>
                                                </li>
                                                <li class="ui-stepInfo">
                                                    <a class="ui-stepSequence"></a>
                                                    <div class="ui-stepName">已完成</div>
                                                    <div class="ui-stepName ui-stepTime">{{ $order->status==cons('order.status.finished')?(new Carbon\Carbon($order->finished_at))->format('Y-m-d H:i'):'' }}</div>
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
                            <table class="table table-bordered table-center table-th-color order-msg-table">
                                <thead>
                                <th>订单号</th>
                                <th>订单金额</th>
                                <th>陈列费/优惠券</th>
                                <th>应付金额</th>
                                <th>支付方式</th>
                                <th>订单状态</th>
                                <th>订单类型</th>
                                <th>备注</th>
                                <th>操作</th>
                                </thead>

                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    <td>¥{{ $order['price'] }}</td>
                                    <td>
                                        {{ $order->coupon_id?' ¥'.bcsub($order->price, $order->after_rebates_price, 2):($order->display_fee > 0?$order->display_fee:'--') }}</td>
                                    <td><span class="orange">¥{{ $order->after_rebates_price }}</span></td>
                                    <td>{{ $order['payment_type'] }}
                                        {{ $order->pay_type==cons('pay_type.cod')?'('.$order->pay_way_lang.')':'' }}
                                    </td>
                                    <td><span class="orange">{{ $order['status_name'] }}</span>
                                        @if($order['pay_status']==cons('order.pay_status.refund_success'))
                                            <a class="iconfont icon-tixing pull-right" title=""
                                               data-container="body" data-toggle="popover" data-placement="bottom"
                                               data-content="退款原因:（{{ $order->orderRefund->reason }}）">
                                            </a>
                                        @endif
                                    </td>
                                    <td><span>{{ $order->type_name }}</span></td>
                                    <td width="15%">{{ !empty($order['remark'])?$order['remark']:'--' }}</td>

                                    <td class="operate">
                                        @if(!$order['is_cancel'])
                                            @if($order->can_confirm)
                                                <p>
                                                    <a class="btn btn-warning ajax" data-method='put'
                                                       data-url="{{ url('api/v1/order/order-confirm/' . $order->id) }}">
                                                        确认订单
                                                    </a>
                                                </p>
                                            @endif
                                            @if($order['can_send'])
                                                <p>
                                                    <a class="btn btn-warning send-goods"
                                                       data-target="#sendModal" data-toggle="modal"
                                                       data-id="{{ $order['id'] }}">
                                                        发货
                                                    </a>
                                                </p>
                                            @elseif($order['can_confirm_collections'])
                                                <p><a class="btn btn-blue ajax" data-method='put'
                                                      data-url="{{ url('api/v1/order/batch-finish-of-sell') }}"
                                                      data-data='{"order_id":{{ $order['id'] }}}'>确认收款</a></p>
                                            @endif
                                            @if($order['can_export'])
                                                <p>
                                                    <a class="btn btn-blue-lighter" target="_blank"
                                                       href="{{ url('order-sell/browser-export?order_id='.$order['id']) }}">打印</a>
                                                </p>
                                                <p>
                                                    <a class="btn btn-blue-lighter"
                                                       href="{{ url('order-sell/export?order_id='.$order['id']) }}">下载</a>

                                                    <br>
                                                    <span class="prompt">（{{ $order->download_count ? '已下载打印' . $order->download_count . '次'  :'未下载' }}
                                                        ）</span>
                                                </p>
                                            @endif
                                            @if($order->can_refund)
                                                <p>
                                                    <a class="btn btn-red refund" data-target="#refund"
                                                       data-toggle="modal"
                                                       data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                        取消并退款
                                                    </a>
                                                </p>
                                            @elseif($order['can_cancel'])
                                                <p>
                                                    <a class="btn btn-red ajax" data-method='put'
                                                       data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                       data-data='{"order_id":{{ $order->id }}}'>
                                                        取消
                                                    </a>
                                                </p>
                                            @elseif($order['can_invalid'])
                                                <p>
                                                    <a class="btn btn-red ajax" data-method='put'
                                                       data-url="{{ url('api/v1/order/invalid/' . $order->id) }}">
                                                        作废
                                                    </a>
                                                </p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{  $order->pay_type==cons('pay_type.pick_up') ? '提货人信息' : '收货人信息' }}</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center table-th-color">
                                <thead>
                                <th>{{ cons()->valueLang('user.type',$order->user->type) }}名称</th>
                                <th>联系人</th>
                                <th>联系电话</th>
                                @if( $order->pay_type!=cons('pay_type.pick_up'))
                                    <th>收货地址</th>
                                @endif
                                </thead>
                                <tr>
                                    <td><p>{{ $order->user_shop_name }}</p>
                                        @if ($order->user && $order->user->shop_id)
                                            <p class="prop-item">
                                                <a href="javascript:"
                                                   onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$order->user->shop_id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                                   class="contact"><span class="iconfont icon-kefu"></span> 联系客户</a>
                                            </p>
                                        @endif
                                    </td>
                                    <td>{{   $order->pay_type!=cons('pay_type.pick_up')?$order->shippingAddress->consigner:$order->user->shop->contact_person }}</td>
                                    <td>{{ $order->pay_type!=cons('pay_type.pick_up')?$order->shippingAddress->phone:$order->user->shop->contact_info }}</td>
                                    @if( $order->pay_type!=cons('pay_type.pick_up'))
                                        <td>
                                            <p> {{  isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}</p>
                                            <p class="prop-item">
                                                <a href="javascript:" data-target="#shippingAddressMapModal"
                                                   data-toggle="modal"
                                                   data-x-lng="{{ isset($order->shippingAddress)? $order->shippingAddress->x_lng : 0 }}"
                                                   data-y-lat="{{ isset($order->shippingAddress)? $order->shippingAddress->y_lat : 0 }}"
                                                   data-address="{{ isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}"
                                                   data-consigner="{{ $order->shippingAddress->consigner }}"
                                                   data-phone= {{ $order->shippingAddress->phone }}>
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
                                <table class="table table-bordered table-center table-th-color">
                                    <thead>
                                    <th>时间</th>
                                    <th>修改人</th>
                                    <th>修改内容</th>
                                    </thead>
                                    @foreach($order->orderChangeRecode->reverse() as $orderChangeRecode)
                                        <tr>
                                            <td>{{ $orderChangeRecode->created_at }}</td>
                                            <td>
                                                <b>{{ $orderChangeRecode->user_id == auth()->id() ? $order->shop->name : $order->deliveryMan()->find($orderChangeRecode->user_id)->pluck('name') }}</b>
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
                            <table class="table table-bordered table-center table-th-color">

                                <thead>
                                <th>商品编号</th>
                                <th>商品图片</th>
                                <th>商品名称</th>
                                <th>商品价格</th>
                                <th>商品数量</th>
                                <th>金额</th>
                                @if($order->can_change_price)
                                    <th>操作</th>
                                @endif
                                </thead>
                                @foreach($orderGoods as $goods)
                                    <tr>
                                        <td>{{ $goods['id'] }}</td>
                                        <td>
                                            <img class="store-img" src={{ $goods['image_url'] }}>
                                        </td>
                                        <td width="30%">
                                            <div class="product-panel">
                                                <a class="product-name"
                                                   href="{{ url('goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                                {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>
                                        </td>
                                        <td>{{ $goods['pivot']['price'] }}
                                            /{{ cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}</td>
                                        <td>{{ 'x'.$goods['pivot']['num'] }}</td>
                                        <td>{{ $goods['pivot']['total_price'] }}</td>
                                        @if($order->can_change_price)
                                            <td><a class="edit change-price" href="javascript:void(0)"
                                                   data-target="#changePrice"
                                                   data-toggle="modal" data-id="{{ $order['id'] }}"
                                                   data-price="{{ $goods->pivot->price }}" ,
                                                   data-num="{{ $goods->pivot->num }}"
                                                   data-pivot="{{  $goods['pivot']['id'] }}"><i
                                                            class="iconfont icon-xiugai"></i>修改</a>
                                                <a class=" red delete-no-form" data-method="delete"
                                                   data-url="{{ url('api/v1/order/goods-delete/' . $goods->pivot->id) }}"
                                                   href="javascript:">
                                                    <i class="iconfont icon-shanchu"></i>删除</a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="{{ $order->can_change_price ? 7 : 6 }}" class="pay-item">
                                        总额 : <span class="red">¥{{ $order->price }}</span>
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
                                <table class="table table-bordered table-center table-th-color">
                                    <thead>
                                    <th>商品编号</th>
                                    <th>商品图片</th>
                                    <th>商品名称</th>
                                    <th>商品数量</th>
                                    </thead>
                                    @foreach($mortgageGoods as $goods)
                                        <tr>
                                            <td>{{ $goods['id'] }}</td>
                                            <td><img class="store-img" src={{ $goods['image_url'] }} /></td>
                                            <td>
                                                <div class="product-panel">
                                                    <a class="product-name"
                                                       href="{{ url('goods/'. $goods['id']) }}">{{ $goods->name }}</a>
                                                    {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                                </div>
                                            </td>
                                            <td>{{ 'x'.$goods['pivot']['num'] }}</td>
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
                            <h3 class="panel-title">订单记录</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center table-th-color">
                                <thead>
                                <th>订单操作</th>
                                <th>操作时间</th>
                                <th>操作人</th>
                                </thead>

                                <tr>
                                    <td>提交订单</td>
                                    <td>{{ $order['created_at'] }}</td>
                                    <td>{{ $order->user_shop_name }}</td>
                                </tr>
                                @if((int)$order['confirm_at'])
                                    <tr>
                                        <td>确认订单</td>
                                        <td>{{ $order['confirm_at'] }}</td>
                                        <td>{{ $order->shop->name }}</td>
                                    </tr>
                                @endif
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
                                @elseif($order->pay_type==cons('pay_type.online'))
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
                                @if($order['status']==cons('order.status.invalid'))
                                    <tr>
                                        <td>作废订单</td>
                                        <td>{{ $order['updated_at'] }}</td>
                                        <td>{{ $order->shop->name }}</td>
                                    </tr>
                                @endif

                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include('includes.order-select-delivery_man')
    @include('includes.order-change-price')
@stop
@include('includes.stepBar')
@section('js')
    @parent
    <script>
        $(function () {
            changePriceByDetailPage();
            deleteNoForm();
            $("[data-toggle='popover']").popover();
        })
    </script>
@stop