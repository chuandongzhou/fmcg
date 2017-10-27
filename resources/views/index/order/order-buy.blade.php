@extends('index.manage-master')
@include('includes.timepicker')
@include('includes.order-refund')
@include('includes.pay')
@section('subtitle', '订单管理')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('order-buy') }}">进货管理</a> >
                    <span class="second-level">订单列表</span>
                </div>
            </div>
            <div class="row wholesalers-management margin-clear">
                @include('index.order.order-buy-menu')
                <form class="form" method="get" action="{{ url('order-buy') }}" autocomplete="off">
                    @if(\Request::is('order-buy'))
                        <div class="col-sm-8 pay-detail search-options">
                            <select name="pay_type" class="ajax-select control">
                                <option value="">全部方式</option>
                                @foreach($pay_type as $key => $value)
                                    <option value="{{ $key }}" {{ $key == array_get($search ,'pay_type') ? 'selected' : ''}}>{{ $value }}</option>
                                @endforeach
                            </select>

                            <select name="status" class="ajax-select control">
                                <option value="">全部状态</option>
                                @foreach($order_status as $key => $value)
                                    <option value="{{ $key }}" {{ $key==array_get($search ,'status') ? 'selected' : ''}}>{{ $value }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="target-url" value="{{ url('order-buy/search') }}"/>
                            <label>下单时间：</label>
                            <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at"
                                   value="{{ array_get($search ,'start_at') }}"
                                   data-format="YYYY-MM-DD"/>　至　
                            <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间"
                                   name="end_at"
                                   value="{{ array_get($search ,'end_at') }}"
                                   data-format="YYYY-MM-DD"/>
                        </div>
                        <div class="col-sm-4 right-search search search-options">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_content" placeholder="供应商、订单号"
                                       value="{{  array_get($search ,'search_content') }}"
                                       aria-describedby="course-search">
                                <span class="input-group-btn">
                    <button class="btn btn-primary ajax-submit search-by-get">搜索</button>
                </span>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-12 table-responsive table-col">
                        @foreach($orders as $order)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="15%">
                                        <label>
                                            <input type="checkbox" name="order_id[]" value="{{ $order['id'] }}"
                                                   class="children">
                                            订单号 : <b>{{ $order['id'] }}</b>
                                        </label>
                                        <span class="order-number">下单时间 :{{ $order['created_at'] }}</span>
                                    </th>
                                    <th width="25%" colspan="4">
                                        <b><a href="{{ url('shop/' . $order->shop_id) }}"
                                              target="_blank">{{ $order->shop_name }}</a></b>
                                        <a href="javascript:"
                                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$order->shop_id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                           class="contact"><span class="iconfont icon-kefu"></span> 联系客服</a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order['goods'] as $key => $goods)
                                    <tr>
                                        <td width="50%">
                                            <img class="store-img" src="{{ $goods['image_url'] }}">

                                            <div class="product-panel">
                                                <a class="product-name ellipsis"
                                                   href="{{  url('goods/' . $goods['id']) }}">{{ $goods->name }}</a>
                                                {!! $goods->is_promotion ? '<div class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</div>' : '' !!}

                                            </div>
                                        </td>
                                        <td width="10%" class="bordered text-center">
                                            <span class="red">¥{{ $goods['pivot']['price'] }}</span>
                                            / {{  cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}
                                        </td>
                                        <td width="10%"
                                            class="bordered text-center">{{ '╳ '.$goods['pivot']['num'] }}</td>
                                        @if(0 == $key)
                                            <td rowspan="{{ count($order['goods'])}}"
                                                class="pay-detail text-center bordered"
                                                width="15%">
                                                <p>{{ $order['status_name'] }}
                                                @if($order['status']==cons('order.status.non_confirm'))
                                                    <p class="prompt">(等待卖家确认)</p>
                                                @endif

                                                <p>{{ $order['payment_type'] }}</p>

                                                <p><span class="red">¥{{ $order['after_rebates_price'] }}</span></p>
                                                </p>
                                            </td>
                                            <td rowspan="{{ count($order['goods'])}}"
                                                class="operating text-center bordered"
                                                width="15%">
                                                <p><a href="{{ url('order-buy/detail?order_id='.$order['id']) }}"
                                                      class="btn btn-blue">查看</a></p>
                                                @if(!$order['is_cancel'])
                                                    @if ($order->can_refund)
                                                        <p>
                                                            <a class="btn btn-danger refund" data-target="#refund"
                                                               data-toggle="modal"
                                                               data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                                退款
                                                            </a>
                                                        </p>
                                                    @elseif($order['can_cancel'])
                                                        <p><a class="btn btn-red ajax"
                                                              data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                              data-method="put"
                                                              data-danger="真的要取消该订单吗？"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                                    @endif
                                                    @if($order['can_payment'])
                                                        <p><a href="javascript:" data-target="#payModal"
                                                              data-toggle="modal"
                                                              class="btn btn-success" data-id="{{ $order->id }}"
                                                              data-price="{{ $order->after_rebates_price }}">去付款</a></p>
                                                    @elseif($order['can_confirm_arrived'])
                                                        <p><a class="btn btn-danger ajax"
                                                              data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
                                                    @endif
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                @if(count($order['goods']) == 0)
                                    <tr>
                                        <td align="center" width="70%">
                                            无订单商品
                                        </td>

                                        <td rowspan="{{ count($order['goods'])}}"
                                            class="pay-detail text-center bordered"
                                            width="15%">
                                            <p>{{ $order['status_name'] }}</p>

                                            <p>{{ $order['payment_type'] }}</p>

                                            <p><span class="red">¥{{ $order['price'] }}</span></p>
                                        </td>
                                        <td rowspan="{{ count($order['goods'])}}" class="operating text-center bordered"
                                            width="15%">
                                            <p><a href="{{ url('order-buy/detail?order_id='.$order['id']) }}"
                                                  class="btn btn-blue">查看</a></p>
                                            @if(!$order['is_cancel'])
                                                @if ($order->can_refund)
                                                    <p>
                                                        <a class="btn btn-danger refund" data-target="#refund"
                                                           data-toggle="modal"
                                                           data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                            退款
                                                        </a>
                                                    </p>
                                                @elseif($order['can_cancel'])
                                                    <p><a class="btn btn-red ajax"
                                                          data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                          data-method="put"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                                @endif
                                                @if($order['can_payment'])
                                                    <p><a href="javascript:" data-target="#payModal" data-toggle="modal"
                                                          class="btn btn-success" data-id="{{ $order->id }}"
                                                          data-price="{{ $order->after_rebates_price }}">去付款</a></p>
                                                @elseif($order['can_confirm_arrived'])
                                                    <p><a class="btn btn-danger ajax"
                                                          data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                                          data-method="put"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                    <div class="col-sm-12 text-right">
                        @if(\Request::is('order-buy'))
                            {!! $orders->appends($search)->render() !!}
                        @else
                            {!! $orders->render() !!}
                        @endif
                    </div>
                    @if(\Request::is('order-buy') && $orders->count())
                        <div class="col-sm-12" id="foot-nav">
                            <input type="checkbox" id="check-all" class="parent"/>
                            <button class="btn btn-red ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                                    data-method="put">
                                批量取消
                            </button>
                            <button class="btn btn-info ajax btn-receive"
                                    data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                    data-method="put">
                                确认收货
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            getOrderButtonEvent();
            onCheckChange('.parent', '.children');
            formSubmitByGet(['order_id[]']);
        })
    </script>
@stop
