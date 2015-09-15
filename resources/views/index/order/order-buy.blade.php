@include('includes.timepicker')
@extends('index.switch')

@section('container')
<div class="container my-goods index wholesalers-management">
    <div class="row">
        @include('index.retailer-left')
        <div class="col-sm-10 wholesalers-management">
            <div class="row ">
                <div class="col-sm-12 notice-bar">
                    <a class="btn ajax-get"
                       data-url="{{ url('order-buy/non-sure') }}">待确认{{ $data['nonSure'] }}</a>
                    <a class="btn ajax-get"
                       data-url="{{ url('order-buy/non-payment') }}">待付款{{ $data['nonPayment'] }}</a>
                    <a class="btn ajax-get"
                       data-url="{{ url('order-buy/non-arrived') }}">待收货{{ $data['nonArrived'] }}</a>
                </div>
                <div class="col-sm-8 pay-detail">
                    <span class="item">支付方式 :
                        <select name="pay_type" class="ajax-select">
                            <option value="">全部方式</option>
                            @foreach($pay_type as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="item">
                        订单状态 :
                          <select name="status" class="ajax-select">
                              <option value="">全部状态</option>
                              @foreach($order_status as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                              @endforeach
                          </select>
                        <input type="hidden" id="target-url" value="{{ url('order-buy/select') }}" />
                    </span>
                    <span class="item">
                        时间段 :
                        <input type="text" class="datetimepicker" placeholder="开始时间" name="start_at" data-format="YYYY-MM-DD" />　至　
                        <input type="text" class="datetimepicker" id="end-time" placeholder="结束时间" name="end_at" data-format="YYYY-MM-DD" />
                    </span>
                </div>
                <div class="col-sm-4 right-search search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号" aria-describedby="course-search">
                        <span class="input-group-btn btn-primary">
                         <button class="btn btn-primary ajax-submit" type="submit" data-url="{{ url('order-buy/search') }}">搜索</button>
                        </span>
                    </div>
                </div>
            </div>
            <form class="ajax-form" method="post">
                <div class="content">
                    @foreach($orders['data'] as $order)
                        <div class="row order-form-list">
                            <div class="col-sm-12 list-title">
                                <input type="checkbox" name="order_id[]" value="{{ $order['id'] }}">
                                <span class="time">{{ $order['created_at'] }}</span>
                                <span>订单号:{{ $order['id'] }}</span>
                                <span>{{ $order['user']['user_name'] or $order['shop']['user']['user_name'] }}</span>
                            </div>
                            <div class="col-sm-8 list-content">
                                <ul>
                                    @foreach($order['goods'] as $good)
                                        <li>
                                            <img src="{{ $good['image_url'] }}">
                                            <a class="product-name" href="#">{{ $good['name'] }}</a>
                                            <span class="red">￥{{ $good['pivot']['price'] }}</span>
                                            <span>{{ $good['pivot']['num'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-sm-2 order-form-detail">
                                <p>订单状态 :{{ $order['status_name'] }}</p>
                                <p>支付方式 :{{ $order['payment_type'] }}</p>
                                <p>订单金额 :<span class="red">￥{{ $order['price'] }}</span></p>
                            </div>
                            <div class="col-sm-2 order-form-operating">
                                <p><a href="{{ url('order-buy/detail/'.$order['id']) }}" class="btn btn-primary">查看</a></p>
                                @if(!$order['is_cancel'])
                                    @if($order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] == cons('order.status.non_send'))
                                        <p><a class="btn btn-cancel ajax" data-url="{{ url('order-buy/cancel-sure') }}"
                                              data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                    @endif
                                    {{--TODO:这里需要跳转支付页面--}}
                                    @if($order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] != cons('order.status.non_sure'))
                                        <p><a href="#" class="btn btn-danger">付款</a></p>
                                    @elseif($order['pay_type'] == cons('pay_type.online') && $order['status'] == cons('order.status.send'))
                                        <p><a class="btn btn-danger ajax" data-url="{{ url('order-buy/batch-finish') }}"
                                              data-method="put" data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-cancel ajax" data-url="{{ url('order-buy/cancel-sure') }}" data-method="put">取消</button>
                        <button class="btn btn-info ajax" data-url="{{ url('order-buy/batch-finish') }}" data-method="put">已收货</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            getOrderList();
        })
    </script>
@stop