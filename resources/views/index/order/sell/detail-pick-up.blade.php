@extends('index.menu-master')
@section('subtitle' , '订单详情')

@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> &rarr;
    订单详情
@stop
@section('right')
    <div class="row order-detail">
        <div class="col-sm-12 go-history">
            <a class="go-back" href="javascript:history.back()"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12">
            <div class="row order-receipt">
                <div class="col-sm-12">
                    <ul class="pull-left order-information">
                        <li class="title">订单信息</li>
                        <li><span class="title-info-name">订单号 : </span>{{ $order['id'] }}</li>
                        <li>
                            <span class="title-info-name">订单金额 : </span><span class="red">¥{{ $order['price'] }}</span>
                        </li>
                        @if(!is_null($order->systemTradeInfo))
                            <li>
                                <span class="title-info-name">订单手续费 : </span><span
                                        class="red">¥{{ $order->systemTradeInfo->target_fee }}</span>
                            </li>
                        @endif
                        <li><span class="title-info-name">支付方式 : </span>{{ $order['payment_type'] }} </li>
                        <li><span class="title-info-name">订单状态 : </span><span
                                    class="red">{{ $order['status_name'] }}</span></li>
                        <li><span class="title-info-name">备注 :</span>

                            <p class="remarks-content">{{ $order['remark'] }}</p>
                        </li>
                    </ul>
                    <div class="pull-right">
                        @if(!$order['is_cancel'])
                            @if($order['can_cancel'])
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
                            @if($order['can_confirm_collections'])
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
                    </div>
                </div>
                <div class="col-sm-12 receiving-information">
                    <ul>
                        <li class="title">提货人信息</li>
                        <li><span class="title-info-name">终端商名称 : </span>{{ $order->user_shop_name }} </li>
                        <li><span class="title-info-name">联系人 : </span>{{  $order->user->shop->contact_person }}
                        </li>
                        <li><span class="title-info-name">联系电话 : </span>{{ $order->user->shop->contact_info }}</li>
                    </ul>
                </div>
                @if(!$order->orderChangeRecode->isEmpty())
                    <div class="col-sm-12 receiving-information">
                        <ul>
                            <li class="title">订单修改记录</li>
                            <li>
                                <ul class="list-update">
                                    @foreach($order->orderChangeRecode->reverse() as $orderChangeRecode)
                                        <li class="item">
                                            <span class="title-info-name"> </span>
                                            {{ $orderChangeRecode->created_at }}
                                            <span class="title-info-name">&nbsp;&nbsp;&nbsp;&nbsp;修改人 : </span>
                                            {{ $orderChangeRecode->user_id == auth()->id() ? $order->shop->name : $order->deliveryMan->name }}
                                        </li>
                                        <li class="item">
                                            {{ $orderChangeRecode->content }}
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            @if($order->orderChangeRecode->count() > 6)
                                <li class="text-center see-more">
                                    <a href="javascript:">点击查看更多</a>
                                </li>
                            @endif
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
                            @if($order->can_change_price)
                                <th>操作</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order['goods'] as $goods)
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
                                <td>{{ $goods['pivot']['price'] }}
                                    / {{ cons()->valueLang('goods.pieces', $goods->pivot->pieces)  }}</td>
                                <td>{{ $goods['pivot']['num'] }}</td>
                                <td>{{ $goods['pivot']['total_price'] }}</td>
                                @if($order->can_change_price)
                                    <td><a class="change-price" href="javascript:void(0)" data-target="#changePrice"
                                           data-toggle="modal" data-id="{{ $order['id'] }}"
                                           data-price="{{ $goods->pivot->price }}" ,
                                           data-num="{{ $goods->pivot->num }}"
                                           data-pivot="{{  $goods['pivot']['id'] }}">修改</a>
                                        <a class="delete-no-form" data-method="delete"
                                           data-url="{{ url('api/v1/order/goods-delete/' . $goods->pivot->id) }}"
                                           href="javascript:">删除</a>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    <p>
                        总额：<b class="red">¥{{ $order->price }}</b>
                        @if($order->coupon_id)
                            <br/> 优惠：<b class="red">¥{{ bcsub($order->price, $order->after_rebates_price, 2) }}</b>
                            <br><span class="prompt-coupon">(满{{ $order->coupon->full }}减 {{ $order->coupon->discount }}
                                )</span>
                            <br/>  应付金额：<b class="red">¥{{ $order->after_rebates_price }}</b>
                        @endif
                    </p>
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
        })
    </script>
@stop