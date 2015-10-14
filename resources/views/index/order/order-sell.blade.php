@include('includes.timepicker')
@extends('index.menu-master')

@section('right')
<div class="col-sm-12 wholesalers-management">
    <div class="row">
        <div class="col-sm-12 notice-bar">
            <a class="btn btn-primary"
               href="{{ url('order-sell') }}">所有订单</a>
            <a class="btn ajax-get"
               data-url="{{ url('order-sell/non-sure') }}">待确认{{ $data['nonSure'] }}</a>
            <a class="btn ajax-get"
               data-url="{{ url('order-sell/non-send') }}">待发货{{ $data['nonSend'] }}</a>
            <a class="btn ajax-get"
               data-url="{{ url('order-sell/pending-collection') }}">待收款{{ $data['pendingCollection'] }}</a>
        </div>
        <div class="col-sm-8 pay-detail search-options">
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
                 <input type="hidden" id="target-url" value="{{ url('order-sell/search') }}" />
            </span>
            <span class="item">
                时间段 :
                <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at" data-format="YYYY-MM-DD" />　至　
                <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间" name="end_at" data-format="YYYY-MM-DD" />
            </span>
        </div>
        <div class="col-sm-4 right-search search search-options">
            <div class="input-group">
                <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary ajax-submit">搜索</button>
                </span>
            </div>
        </div>
    </div>
    <form class="form" method="get" action="{{ url('order-sell/export') }}">
        <div class="row order-table-list">
            <div class="col-sm-12 table-responsive table-col">
                <div class="content">
                @foreach($orders['data'] as $order)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <label><input type="checkbox" name="order_id[]" value="{{ $order['id'] }}">{{ $order['created_at'] }}</label>
                                    <span class="order-number">订单号:{{ $order['id'] }}</span>
                                </th>
                                <th>{{ $order['user']['shop']['name'] }}</th>
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
                                        <a class="product-name" href="{{  url('goods/'.$good['id']) }}">{{ $good['name'] }}</a>
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
                                            <p><a href="{{ url('order-sell/detail/'.$order['id']) }}" class="btn btn-primary">查看</a></p>
                                            @if(!$order['is_cancel'])
                                                @if($order['status'] == cons('order.status.non_sure'))
                                                    <p><a class="btn btn-danger ajax" data-method = 'put' data-url="{{ url('order-sell/batch-sure') }}"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>确认</a></p>
                                                @elseif($order['pay_type'] == cons('order.pay_type.online') && $order['pay_status'] == cons('order.pay_status.non_payment') && $order['status'] == cons('order.status.non_send'))
                                                    <p><a class="btn btn-cancel ajax" data-method = 'put' data-url="{{ url('order-sell/cancel-sure') }}"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>取消</a></p>
                                                @elseif(($order['pay_type'] == cons('pay_type.online')&& $order['pay_status'] == cons('order.pay_status.payment_success') && $order['status'] ==  cons('order.status.non_send'))
                                                 || ($order['pay_type'] == cons('pay_type.cod')&& $order['status'] == cons('order.status.non_send')))
                                                    <p><a class="btn btn-warning ajax" data-method = 'put' data-url="{{ url('order-sell/batch-send') }}"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>发货</a></p>
                                                @elseif($order['pay_type'] == cons('pay_type.cod') && $order['pay_status'] == cons('order.pay_status.payment_success') && $order['status'] == cons('order.status.send'))
                                                    <p><a class="btn btn-info ajax" data-method = 'put' data-url="{{ url('order-sell/batch-finish') }}"
                                                          data-data='{"order_id":{{ $order['id'] }}}'>收款</a></p>
                                                @endif
                                            @endif
                                            <p><a target="_blank" class="btn btn-success" href="{{ url('order-sell/export?order_id='.$order['id']) }}">导出</a></p>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                </div>
            </div>
        </div>
        @if($orders['data'])
        <div class="row" id="foot-nav">
            <div class="col-sm-12 padding-clear">
                <button class="btn btn-danger ajax" data-url="{{ url('order-sell/batch-sure') }}" data-method="put">确认</button>
                <button class="btn btn-cancel ajax" data-url="{{ url('order-sell/cancel-sure') }}" data-method="put">取消</button>
                <button class="btn btn-success" >导出</button>
                <button class="btn btn-warning ajax" data-url="{{ url('order-sell/batch-send') }}" data-method="put">发货</button>
                <button class="btn btn-info ajax" data-url="{{ url('order-sell/batch-finish') }}" data-method="put">收款</button>
            </div>
        </div>
        @endif
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