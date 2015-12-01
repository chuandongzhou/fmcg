@include('includes.timepicker')
@extends('index.menu-master')

@section('subtitle', '订单管理')

@section('right')
    <div class="col-sm-12 wholesalers-management">
        <div class="row">
            <div class="col-sm-12 notice-bar">
                <a class="btn btn-primary"
                   href="{{ url('order-sell') }}">所有订单</a>
                <a class="btn ajax-get"
                   data-url="{{ url('api/v1/order/non-send') }}">待发货{{ $data['nonSend'] }}</a>
                <a class="btn ajax-get"
                   data-url="{{ url('api/v1/order/pending-collection') }}">待收款{{ $data['pendingCollection'] }}</a>
            </div>
        </div>
        <form class="form" method="get" action="{{ url('order-sell/index') }}" autocomplete="off">
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
                          <option value="{{ $key }} " {{ $key==$search['status'] ? 'selected' : ''}}>{{ $value }}</option>
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
                    <button class="btn btn-primary ajax-submit">搜索</button>
                </span>
                </div>
            </div>

            <input type="hidden" name="order_id" value=""/>

            <div class="row order-table-list">
                <div class="col-sm-12 table-responsive table-col">
                    <div class="content">
                        @foreach($orders as $order)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <label><input type="checkbox" class="order_id" name="order_id[]"
                                                      value="{{ $order['id'] }}">{{ $order['created_at'] }}</label>
                                        <span class="order-number">订单号:{{ $order['id'] }}</span>
                                    </th>
                                    <th>{{ $order['user']['shop']['name'] }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->goods as $key => $good)
                                    <tr>
                                        <td>
                                            <img class="store-img" src="{{ $good->image_url }}">
                                            <a class="product-name"
                                               href="{{  url('goods/'.$good['id']) }}">{{ $good['name'] }}</a>
                                        </td>
                                        <td class="red">￥{{ $good['pivot']['price'] }}</td>
                                        <td>{{ $good['pivot']['num'] }}</td>
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
                                                        <a class="btn btn-warning ajax" data-method='put'
                                                           data-url="{{ url('api/v1/order/order-confirm/' . $order->id) }}">
                                                            确认订单
                                                        </a>
                                                    @endif
                                                    @if($order['can_send'])
                                                        <p><a class="btn btn-warning send-goods"
                                                              data-target="#sendModal" data-toggle="modal"
                                                              data-data="{{ $order['id'] }}">发货</a></p>
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
                                data-method="put" data-done-then="none">批量取消
                        </button>
                        <a class="btn btn-warning" data-target="#sendModal" data-toggle="modal">批量发货</a>
                        <button class="btn btn-info ajax btn-receive"
                                data-url="{{ url('api/v1/order/batch-finish-of-sell') }}" data-method="put">确认收款
                        </button>
                        <button class="btn btn-success export" data-url="{{ url('order-sell/export') }}"
                                data-method="get">导出
                        </button>
                    </div>
                </div>
            @endif
            <div class="modal fade in" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
                 aria-hidden="true" style="padding-right: 17px;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" style="width:70%;margin:auto">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                            @if($delivery_man->count())
                                <p class="modal-title" id="cropperModalLabel">选择配送人员:
                            <span class="extra-text">
                                  <select name="delivery_man_id">
                                      @foreach($delivery_man as $index => $item)
                                          <option value="{{ $index }}">{{ $item }}</option>
                                      @endforeach
                                  </select>
                            </span>
                                </p>
                            @else
                                没有配送人员信息,请设置。<a href="{{ url('personal/delivery-man') }}">去设置</a>
                            @endif
                        </div>
                        <div class="modal-body">
                            <div class="text-right">
                                <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                                </button>
                                @if($delivery_man->count())
                                    <button type="button" class="btn btn-primary btn-sm btn-add ajax btn-send"
                                            data-text="确定" data-url="{{ url('api/v1/order/batch-send') }}"
                                            data-method="put">确定
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
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
            getOrderButtonEvent();
        })
    </script>
@stop