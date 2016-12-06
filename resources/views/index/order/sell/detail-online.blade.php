@extends('index.menu-master')
@section('subtitle' , '订单详情')
@include('includes.shipping-address-map')
@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> &rarr;
    订单详情
@stop

@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="javascript:history.back()"><i class="iconfont icon-fanhui"></i> 返回</a>
        </div>
        <div class="col-sm-12">
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
                                                <div class="ui-stepName">未付款</div>
                                            </li>
                                            <li class="ui-stepInfo">
                                                <a class="ui-stepSequence"></a>
                                                <div class="ui-stepName">已付款</div>
                                            </li>
                                            <li class="ui-stepInfo">
                                                <a class="ui-stepSequence"></a>
                                                <div class="ui-stepName">已发货</div>
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
                                    <th>优惠券</th>
                                    <th>应付金额</th>
                                    @if(!is_null($order->systemTradeInfo))
                                        <th>订单手续费</th>
                                    @endif
                                    <th>支付方式</th>
                                    <th>订单状态</th>
                                    <th>备注</th>
                                    <td rowspan="2">
                                        @if(!$order['is_cancel'])
                                            @if($order->can_refund)
                                                <p>
                                                    <a class="btn btn-danger refund" data-target="#refund"
                                                       data-toggle="modal"
                                                       data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                        取消并退款
                                                    </a>
                                                </p>
                                            @elseif($order['can_cancel'])
                                                <p>
                                                    <a class="btn btn-cancel ajax" data-method='put'
                                                       data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                       data-data='{"order_id":{{ $order['id'] }}}'>
                                                        取消
                                                    </a>
                                                </p>
                                            @endif
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
                                                <p><a class="btn btn-info ajax" data-method='put'
                                                      data-url="{{ url('api/v1/order/batch-finish-of-sell') }}"
                                                      data-data='{"order_id":{{ $order['id'] }}}'>确认收款</a></p>
                                            @endif
                                            @if($order['can_export'])
                                                <p>
                                                    <a class="btn btn-success print" target="_blank"
                                                       href="{{ url('order-sell/browser-export?order_id='.$order['id']) }}">打印</a>
                                                </p>
                                                <p>
                                                    <a class="btn btn-success"
                                                       href="{{ url('order-sell/export?order_id='.$order['id']) }}">下载</a>

                                                    <br>
                                    <span class="prompt">（{{ $order->download_count ? '已下载打印' . $order->download_count . '次'  :'未下载' }}
                                        ）</span>
                                                </p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    <td>￥{{ $order['price'] }}</td>
                                    <td>{{ $order->coupon_id?'￥'.bcsub($order->price, $order->after_rebates_price, 2):'' }}</td>
                                    <td><span class="orange">￥{{ $order->after_rebates_price }}</span></td>
                                    @if(!is_null($order->systemTradeInfo))
                                        <td>￥{{ $order->systemTradeInfo->target_fee }}</td>
                                    @endif
                                    <td>{{ $order['payment_type'] }}</td>
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
                            <h3 class="panel-title">收货人信息</h3>
                        </div>
                        <div class="panel-container table-responsive">
                            <table class="table table-bordered table-center">
                                <tr>
                                    <th>终端商名称</th>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>收货地址</th>
                                </tr>
                                <tr>
                                    <td><p>{{ $order->user_shop_name }}</p>
                                        <p class="prop-item"><a href="javascript:"
                                                                onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$order->user->shop_id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                                                class="contact"><span class="iconfont icon-kefu"></span>
                                                联系客户</a></p>
                                    </td>
                                    <td>{{$order->shippingAddress->consigner }}</td>
                                    <td>{{  $order->shippingAddress->phone }}</td>
                                    <td>
                                        <p>{{ isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}</p>
                                        <p class="prop-item"><a href="javascript:"
                                                                data-target="#shippingAddressMapModal"
                                                                data-toggle="modal"
                                                                data-x-lng="{{ isset($order->shippingAddress)? $order->shippingAddress->x_lng : 0 }}"
                                                                data-y-lat="{{ isset($order->shippingAddress)? $order->shippingAddress->y_lat : 0 }}"
                                                                data-address="{{ isset($order->shippingAddress->address) ? $order->shippingAddress->address->address_name : '' }}"
                                                                data-consigner="{{ $order->shippingAddress->consigner }}"
                                                                data-phone= {{ $order->shippingAddress->phone }}
                                            ><i class="iconfont icon-chakanditu"></i> 查看地图</a></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @if((int)$order['send_at'])
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
                                                <b>{{ $orderChangeRecode->user_id == auth()->id() ? $order->shop->name : $order->deliveryMan()->find($orderChangeRecode->user_id)->pluck('name') }}</b>
                                            </td>
                                            <td>{{ $orderChangeRecode->content }}</td>
                                        </tr>>
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
                                <tr>
                                    <td>324</td>
                                    <td><img class="store-img"
                                             src="http://192.168.2.66//upload/file/2016/06/02/574faa6603e88.png?1464838757">
                                    </td>
                                    <td width="30%">
                                        <div class="product-panel">
                                            <a class="product-name" href="">植物大战僵尸，只有想不到，没有买不到~~~别说我没告诉你哟</a>
                                            <p class="promotions">(<span
                                                        class="ellipsis"> 新一批萌萌哒植物震撼来袭，“魔镜魔镜，谁最美，当然是你！”</span>)</p>
                                        </div>
                                    </td>
                                    <td>16.00/ 听</td>
                                    <td>3</td>
                                    <td>48.00</td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="pay-item">
                                        <div class="money">总额 : <span class="red">￥2100.00</span></div>
                                        <button class="btn btn-blue-lighter">去付款</button>
                                    </td>
                                </tr>
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
@include('includes/stepBar')
@section('js')
    @parent
    <script>
        $(function () {
            changePriceByDetailPage();
            deleteNoForm();
        })
    </script>
@stop