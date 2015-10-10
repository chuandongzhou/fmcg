@include('includes.timepicker')
@extends('index.menu-master')

@section('right')
    <div class="col-sm-12 wholesalers-management">
        <div class="row">
            <div class="col-sm-12 notice-bar">
                <a class="btn btn-primary"
                   href="{{ url('order-buy') }}">所有订单</a>
                <a class="btn ajax-get"
                   data-url="{{ url('order-buy/non-sure') }}">待确认{{ $data['nonSure'] }}</a>
                <a class="btn ajax-get"
                   data-url="{{ url('order-buy/non-payment') }}">待付款{{ $data['nonPayment'] }}</a>
                <a class="btn ajax-get"
                   data-url="{{ url('order-buy/non-arrived') }}">待收货{{ $data['nonArrived'] }}</a>
            </div>
            <div class="col-sm-8 pay-detail">
            <span class="item control-item">支付方式 :
                <select name="pay_type" class="ajax-select control">
                    <option value="">全部方式</option>
                    @foreach($pay_type as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </span>
            <span class="item control-item">
                订单状态 :
                  <select name="status" class="ajax-select control">
                      <option value="">全部状态</option>
                      @foreach($order_status as $key => $value)
                          <option value="{{ $key }}">{{ $value }}</option>
                      @endforeach
                  </select>
                <input type="hidden" id="target-url" value="{{ url('order-buy/select') }}"/>
            </span>
            <span class="item">
                时间段 :
                <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at"
                       data-format="YYYY-MM-DD"/>　至　
                <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间" name="end_at"
                       data-format="YYYY-MM-DD"/>
            </span>
            </div>
            <div class="col-sm-4 right-search search">
                <div class="input-group">
                    <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号"
                           aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                 <button class="btn btn-primary ajax-submit" type="submit" data-url="{{ url('order-buy/search') }}">搜索
                 </button>
                </span>
                </div>
            </div>
        </div>
        <form class="ajax-form" method="post">
            <div class="row order-table-list">
                <div class="col-sm-12 table-responsive table-col">
                    <div class="content">
                        @foreach($orders['data'] as $order)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <label><input type="checkbox" name="order_id[]"
                                                      value="{{ $order['id'] }}">{{ $order['created_at'] }}</label>
                                        <span class="order-number">订单号:{{ $order['id'] }}</span>
                                    </th>
                                    <th>{{ $order['user']['user_name'] or $order['shop']['user']['user_name'] }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order['goods'] as $key => $good)
                                    <tr>
                                        <td>
                                            <img class="store-img" src="{{ $good['image_url'] }}">
                                            <a class="product-name"
                                               href="{{  url('goods/'.$good['id']) }}">{{ $good['name'] }}</a>
                                        </td>
                                        <td class="red">￥{{ $good['pivot']['price'] }}</td>
                                        <td>{{ $good['pivot']['num'] }}</td>
                                        @if(0 == $key )
                                            <td rowspan="{{ count($order['goods'])}}" class="pay-detail text-center">
                                                <p>订单状态 :{{ $order['status_name'] }}</p>

                                                <p>支付方式 :{{ $order['payment_type'] }}</p>

                                                <p>订单金额 :<span class="red">￥{{ $order['price'] }}</span></p>
                                            </td>
                                            <td rowspan="{{ count($order['goods'])}}" class="operating text-center">
                                                <p><a href="{{ url('order-buy/detail/'.$order['id']) }}"
                                                      class="btn btn-primary">查看</a></p>
                                                @if(!$order['is_cancel'])
                                                    @if($order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] == cons('order.status.non_send'))
                                                        <p><a class="btn btn-cancel ajax"
                                                              data-url="{{ url('order-buy/cancel-sure') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                                    @endif
                                                    {{--TODO:这里需要跳转支付页面--}}
                                                    @if($order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] == cons('order.status.non_send') && $order['pay_type'] == cons('pay_type.online'))
                                                        <p><a href="{{ url('pay/request/' . $order['id']) }}"
                                                              class="btn btn-danger">付款</a></p>
                                                    @elseif($order['pay_type'] == cons('pay_type.online') && $order['status'] == cons('order.status.send'))
                                                        <p><a class="btn btn-danger ajax"
                                                              data-url="{{ url('order-buy/batch-finish') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
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
            <div class="row">
                <div class="col-sm-12 padding-clear">
                    <button class="btn btn-cancel ajax" data-url="{{ url('order-buy/cancel-sure') }}" data-method="put">
                        取消
                    </button>
                    <button class="btn btn-info ajax" data-url="{{ url('order-buy/batch-finish') }}" data-method="put">
                        已收货
                    </button>
                </div>
            </div>
        </form>
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
