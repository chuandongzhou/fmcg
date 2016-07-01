@extends('index.menu-master')
@include('includes.timepicker')
@include('includes.order-refund')
@include('includes.pay')
@section('subtitle', '订单管理')
@if(auth()->user()->type == cons('user.type.retailer'))
    @section('top-title', '订单管理->订单列表')
@else
    @section('top-title', '进货管理->订单列表')
@endif

@section('right')
    <div class="row">
    <div class="col-sm-12 wholesalers-management">
        <div class="row">
            @include('index.order.order-buy-menu')
        </div>
        <form class="form" method="get" action="{{ url('order-buy') }}" autocomplete="off">
            @if(\Request::is('order-buy'))
                <div class="col-sm-8 pay-detail search-options">
                <span class="item control-item">支付方式 :
                    <select name="pay_type" class="ajax-select control">
                        <option value="">全部方式</option>
                        @foreach($pay_type as $key => $value)
                            <option value="{{ $key }}" {{ $key==$search['pay_type'] ? 'selected' : ''}}>{{ $value }}</option>
                        @endforeach
                    </select>
                </span>
                <span class="item control-item">
                    订单状态 :
                      <select name="status" class="ajax-select control">
                          <option value="">全部状态</option>
                          @foreach($order_status as $key => $value)
                              <option value="{{ $key }}" {{ $key==$search['status'] ? 'selected' : ''}}>{{ $value }}</option>
                          @endforeach
                      </select>
                    <input type="hidden" id="target-url" value="{{ url('order-buy/search') }}"/>
                </span>
                <span class="item">
                    时间段 :
                    <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at"
                           value="{{ $search['start_at'] or '' }}"
                           data-format="YYYY-MM-DD"/>　至　
                    <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间" name="end_at"
                           value="{{ $search['end_at'] or '' }}"
                           data-format="YYYY-MM-DD"/>
                </span>
                </div>
                <div class="col-sm-4 right-search search search-options">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_content" placeholder="供应商、订单号"
                               value="{{ $search['search_content'] or '' }}"
                               aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                 <button class="btn btn-primary ajax-submit search-by-get">搜索
                 </button>
                </span>
                    </div>
                </div>
            @endif
            <div class="row order-table-list">
                <div class="col-sm-12 table-responsive table-col">
                    <div class="content">
                        @foreach($orders as $order)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="50%">
                                        <label>
                                            <input type="checkbox" name="order_id[]" value="{{ $order['id'] }}"
                                                   class="children">
                                            {{ $order['created_at'] }}
                                        </label>
                                        <span class="order-number"> 订单号 : {{ $order['id'] }}</span>
                                    </th>
                                    <th colspan="3" width="30%"> {{ $order['shop']['name'] }} </th>
                                    <th>
                                        <a href="javascript:"
                                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$order->shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                           class="contact"><span class="fa fa-commenting-o"></span> 联系客服</a>
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
                                                {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>

                                        </td>
                                        <td width="15%"><span class="red">￥{{ $goods['pivot']['price'] }}</span>
                                            / {{  cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}</td>
                                        <td width="5%">{{ $goods['pivot']['num'] }}</td>
                                        @if(0 == $key)
                                            <td rowspan="{{ count($order['goods'])}}" class="pay-detail text-center">
                                                <p>{{ $order['status_name'] }}</p>

                                                <p>{{ $order['payment_type'] }}</p>

                                                <p><span class="red">￥{{ $order['price'] }}</span></p>
                                            </td>
                                            <td rowspan="{{ count($order['goods'])}}" class="operating text-center">
                                                <p><a href="{{ url('order-buy/detail?order_id='.$order['id']) }}"
                                                      class="btn btn-primary">查看</a></p>
                                                @if(!$order['is_cancel'])
                                                    @if($order['can_cancel'])
                                                        <p><a class="btn btn-cancel ajax"
                                                              data-url="{{ url('api/v1/order/cancel-sure') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                                    @endif
                                                    @if($order['can_payment'])
                                                        <p><a href="javascript:" data-target="#payModal" data-toggle="modal"
                                                              class="btn btn-success" data-id="{{ $order->id }}" data-price="{{ $order->price }}">去付款</a></p>
                                                    @elseif($order['can_confirm_arrived'])
                                                        <p><a class="btn btn-danger ajax"
                                                              data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
                                                    @endif
                                                    @if ($order->can_refund)
                                                        <p>
                                                            <a class="btn btn-danger refund" data-target="#refund"
                                                               data-toggle="modal"
                                                               data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                                退款
                                                            </a>
                                                        </p>
                                                    @endif
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="text-right">
                @if(\Request::is('order-buy'))
                    {!! $orders->appends(array_filter($search))->render() !!}
                @else
                    {!! $orders->render() !!}
                @endif
            </div>

            @if(\Request::is('order-buy') && $orders->count())
                <div class="row" id="foot-nav">
                    <div class="col-sm-12 padding-clear">
                        <input type="checkbox" id="check-all" class="parent"/>
                        <button class="btn btn-cancel ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                                data-method="put">
                            批量取消
                        </button>
                        <button class="btn btn-info ajax btn-receive"
                                data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                data-method="put">
                            确认收货
                        </button>
                    </div>
                </div>
            @endif
        </form>
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
