@include('includes.timepicker')
@extends('index.menu-master')

@section('subtitle', '订单管理')

@section('right')
    <div class="col-sm-12 wholesalers-management">
        <div class="row">
            <div class="col-sm-12 notice-bar">
                <a class="btn btn-primary"
                   href="{{ url('order-buy') }}">所有订单</a>

                <a class="btn ajax-get"
                   data-url="{{ url('api/v1/order/non-payment') }}"
                   data-limit="nonPayment">待付款{{ $data['nonPayment'] }}</a>
                <a class="btn ajax-get"
                   data-url="{{ url('api/v1/order/non-arrived') }}"
                   data-limit="nonArrived">待收货{{ $data['nonArrived'] }}</a>
            </div>
        </div>
        <form class="form" method="get" action="{{ url('order-buy/index') }}" autocomplete="off">
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
                    <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号"
                           value="{{ $search['search_content'] or '' }}"
                           aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                 <button class="btn btn-primary ajax-submit">搜索
                 </button>
                </span>
                </div>
            </div>

            <div class="row order-table-list">
                <div class="col-sm-12 table-responsive table-col">
                    <div class="content">
                        @foreach($orders as $order)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <label>
                                            <input type="checkbox" name="order_id[]" value="{{ $order['id'] }}">
                                            {{ $order['created_at'] }}
                                        </label>
                                        <span class="order-number"> 订单号 : {{ $order['id'] }}</span>
                                    </th>
                                    <th colspan="4"> {{ $order['shop']['name'] }} </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order['goods'] as $key => $good)
                                    <tr>
                                        <td>
                                            <img class="store-img" src="{{ $good['image_url'] }}">
                                            <a class="product-name" href="{{  url('goods/' . $good['id']) }}">
                                                {{ $good['name'] }}
                                            </a>
                                        </td>
                                        <td class="red">￥{{ $good['pivot']['price'] }}</td>
                                        <td>{{ $good['pivot']['num'] }}</td>

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
                                                        <p><a href="{{ url('pay/request/' . $order['id']) }}"
                                                              class="btn btn-success" target="_blank">去付款</a></p>
                                                    @elseif($order['can_confirm_arrived'])
                                                        <p><a class="btn btn-danger ajax"
                                                              data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                                              data-method="put"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>确认收货</a></p>
                                                    @endif
                                                    @if ($order->can_refund)
                                                        <p>
                                                            <a class="btn btn-danger ajax"
                                                               data-url="{{ url('api/v1/pay/refund/' . $order->id) }}"
                                                               data-method="put">
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
            {!! $orders->appends(['pay_type' => $search['pay_type'],'status'=>$search['status'],'start_at'=>$search['start_at'],'end_at'=>$search['end_at'],'search_content'=>$search['search_content']])->render() !!}
            @if($orders->count())
                <div class="row" id="foot-nav">
                    <div class="col-sm-12 padding-clear">
                        <input type="checkbox" id="check-all"/>
                        <button class="btn btn-cancel ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                                data-method="put">
                            批量取消
                        </button>
                        <button class="btn btn-info ajax btn-receive" data-url="{{ url('api/v1/order/batch-finish-of-buy') }}"
                                data-method="put">
                            确认收货
                        </button>
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
            getOrderButtonEvent();
        })
    </script>
@stop
