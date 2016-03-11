@extends('index.menu-master')
@include('includes.timepicker')
@include('includes.order-refund')
@section('subtitle', '订单管理')

@section('right')
    <div class="col-sm-12 wholesalers-management">
        <div class="row">
            @include('index.order.order-sell-menu')
        </div>
        <form class="form" method="get" action="{{ url('order-sell') }}" autocomplete="off">
            @if (\Request::is('order-sell'))
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
            </span>
            <span class="item">
                时间段 :
                <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at"
                       data-format="YYYY-MM-DD" value="{{ $search['start_at'] or '' }}"/>　至　
                <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间" name="end_at"
                       data-format="YYYY-MM-DD" value="{{ $search['end_at'] or '' }}"/>
            </span>
                </div>
                <div class="col-sm-4 right-search search search-options">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号"
                               aria-describedby="course-search" value="{{ $search['search_content'] or '' }}">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary ajax-submit search-by-get">搜索</button>
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
                                    <th>
                                        <label>
                                            <input type="checkbox" class="order_id" name="order_id[]"
                                                   value="{{ $order['id'] }}">{{ $order['created_at'] }}
                                        </label>
                                        <span class="order-number">订单号 : {{ $order['id'] }}</span>
                                    </th>
                                    <th>{{ $order['user']['shop']['name'] }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->goods as $key => $goods)
                                    <tr>
                                        <td>
                                            <img class="store-img" src="{{ $goods->image_url }}">

                                            <div class="product-panel">
                                                <a class="product-name ellipsis"
                                                   href="{{  url('my-goods/' . $goods['id']) }}">{{ $goods->name }}</a>
                                                {!! $goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="red">￥{{ $goods['pivot']['price'] }}</span>
                                            / {{ cons()->valueLang('goods.pieces' , $goods->{'pieces_' . $order->user->type_name})  }}
                                        </td>
                                        <td>{{ $goods['pivot']['num'] }}</td>
                                        @if(0 == $key )
                                            <td rowspan="{{ count($order['goods'])}}" class="pay-detail text-center">
                                                <p>{{ $order['status_name'] }}</p>

                                                <p>{{ $order['payment_type'] }}</p>

                                                <p><span class="red">￥{{ $order['price'] }}</span></p>
                                            </td>
                                            <td rowspan="{{ count($order['goods'])}}" class="operating text-center">
                                                <p><a href="{{ url('order-sell/detail?order_id='.$order['id']) }}"
                                                      class="btn btn-primary">查看</a></p>
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
                                                        <p>
                                                            <a class="btn btn-danger refund" data-target="#refund"
                                                               data-toggle="modal"
                                                               data-url="{{ url('api/v1/pay/refund/' . $order->id) }}">
                                                                取消并退款
                                                            </a>
                                                        </p>
                                                    @endif
                                                    @if($order['can_send'])
                                                        <p>
                                                            <a class="btn btn-warning send-goods"
                                                               data-target="#sendModal" data-toggle="modal"
                                                               data-data="{{ $order['id'] }}">
                                                                发货
                                                            </a>
                                                        </p>
                                                    @elseif($order['can_confirm_collections'])
                                                        <p><a class="btn btn-info ajax" data-method='put'
                                                              data-url="{{ url('api/v1/order/batch-finish-of-sell') }}"
                                                              data-data='{"order_id":{{ $order['id'] }}}'>确认收款</a></p>
                                                    @endif
                                                    @if($order['can_export'])
                                                        <p><a target="_blank" class="btn btn-success"
                                                              href="{{ url('order-sell/export?order_id='.$order['id']) }}">导出</a>
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
                @if(\Request::is('order-sell'))
                    {!! $orders->appends(array_filter($search))->render() !!}
                @else
                    {!! $orders->render() !!}
                @endif
            </div>
            @if(\Request::is('order-sell') && $orders->count() )
                <div class="row" id="foot-nav">
                    <div class="col-sm-12 padding-clear">
                        <input type="checkbox" id="check-all"/>
                        <button class="btn btn-cancel ajax" data-url="{{ url('api/v1/order/cancel-sure') }}"
                                data-method="put" data-done-then="none">批量取消
                        </button>
                        <a class="btn btn-warning" data-target="#sendModal" data-toggle="modal">批量发货</a>
                        <button class="btn btn-info ajax btn-receive"
                                data-url="{{ url('api/v1/order/batch-finish-of-sell') }}" data-method="put">确认收款
                        </button>
                        <a class="btn btn-success export" data-url="{{ url('order-sell/export') }}"
                           data-method="get">导出
                        </a>
                    </div>
                </div>
            @endif
            @include('includes.order-select-delivery_man')
        </form>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            getOrderButtonEvent();
            formSubmitByGet(['delivery_man_id']);
            @if(session('export_error'))
                alert('{{ session('export_error') }}');
            @endif

            $('.refund').click(function () {
                var obj = $(this), url = obj.data('url');
                $('.modal-footer').find('button[type="submit"]').attr('data-url', url).attr('data-data', '{"is_seller" : true}');
            })
        })
    </script>
@stop