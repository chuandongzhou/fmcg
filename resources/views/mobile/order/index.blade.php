@extends('mobile.master')

@section('subtitle', '订单列表')

@include('includes.order-refund')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item white-bg">
        <div class="row nav-top color-black">
            <div class="col-xs-12">{{ $pageName }}</div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m60">
        @foreach($orders as $order)
            <div class="row all-orders-list">
                <div onclick="window.location.href='{{ url('order/' . $order->id) }}'">
                    <div class="col-xs-12 list-item">
                        <div class="item order-title-panel">
                            <div class="pull-left">订货单 <span
                                        class="color-black">{{ $order->id }}{{ $order->type? '(自主)' : '' }}</span></div>
                            <div class="pull-right">{{ $order->created_at }}</div>
                        </div>
                    </div>
                    <div class="col-xs-12 list-item">
                        <div class="item">
                            <a class="pull-left shop-name">{{ $order->shop_name }}
                                <i class="iconfont icon-jiantouyoujiantou"></i>
                            </a>
                            <div class="pull-right">
                                <span class="red">{{ $order->status_name }}{{ $order->pay_type == cons('pay_type.pick_up') ? '(自提)' : '' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 list-item">
                        <div class="row bordered">
                            @foreach($order->goods as $goods)
                                <div class="col-xs-12 item">
                                    <img src="{{ $goods->image_url }}" class="pull-left commodity-img"/>

                                    <div class="commodity-name pull-left">{{ $goods->name }}</div>
                                    <div class="right-num-panel pull-right">
                                        <div>x{{ $goods->pivot->num }}</div>
                                        <div>
                                            ¥{{ $goods->pivot->price }}
                                            /{{  cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 list-item">
                    <div class="item">
                        <div class="pull-left">共{{ $order->goods->count() }}件商品</div>
                        <div class="pull-right text-right">
                            <div>总额：<span class="red">¥{{ $order->after_rebates_price }}</span></div>
                            <div>
                                @if ($order->can_refund)
                                    <button class="btn btn-danger refund" data-target="#refund"
                                            data-toggle="modal"
                                            data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">退款
                                    </button>

                                @elseif($order->can_cancel)
                                    <button type="button" class="btn btn-primary mobile-ajax"
                                            data-url="{{ url('api/v1/order/cancel-sure') }}"
                                            data-method="put"
                                            data-danger="真的要取消该订单吗？"
                                            data-data='{"order_id":{{ $order['id'] }}}'>取消
                                    </button>
                                @endif

                                @if($order->can_payment)
                                    <a type="button" class="btn btn-success" href="{{ url('pay/' . $order->id)}}">
                                        去付款
                                    </a>
                                @elseif($order->can_confirm_arrived)
                                    <button type="button" class="btn btn-danger mobile-ajax"
                                            data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                            data-method="put"
                                            data-data='{"order_id":{{ $order['id'] }}}'>确认收货
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-xs-12 text-center  loading-image hidden">
            <i class="fa fa-spinner fa-pulse"></i>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        //滑到最底部时加载商品
        var loadingImage = $('.loading-image'), page = 1;
        function loadOrders() {
            if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                var url = window.location.href, urls = url.split('?'), queryString = urls[1];
                queryString = queryString === undefined ? '' : queryString.replace(/[\&\?]*page=\d+/, '');
                loadingImage.removeClass('hidden');
                document.removeEventListener('touchmove', loadOrders, false);
                var pivot = queryString ? '&' : '';
                queryString = queryString + pivot + 'page=' + (page + 1);
                $.ajax({
                    url: urls[0],
                    method: 'get',
                    data: queryString
                }).done(
                    function (data) {
                        loadingImage.before(data.html);
                        if (data.count) {
                            page = page + 1
                            loadingImage.addClass('hidden');
                            document.addEventListener('touchmove', loadOrders, false);
                        } else {
                            loadingImage.html('没有更多数据')
                        }
                    }
                ).fail(function () {
                    loadingImage.addClass('hidden');
                    document.addEventListener('touchmove', loadOrders, false);
                    showMassage('服务器错误');
                });

            }
        }
        document.addEventListener('touchmove', loadOrders, false);
    </script>
@stop