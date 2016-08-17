@extends('index.menu-master')
@section('subtitle', '业务管理-订货单')
@include('includes.timepicker')


@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    订货单
@stop

@section('right')
    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal" data-done-then="refresh" action="{{ url('business/order/order-forms') }}"
                  method="get" autocomplete="off">
                <div class="form-group editor-item">
                    <div class="col-sm-6">
                        <div class="col-sm-5  padding-clear">
                            <input name="start_date" value="{{ $data['start_date'] or '' }}" placeholder="请输入开始日期"
                                   type="text" class="form-control datetimepicker" data-format="YYYY-MM-DD">
                        </div>
                        <div class="col-sm-1 padding-clear">
                            <label class="control-label col-sm-1">至</label>
                        </div>
                        <div class="col-sm-5 padding-clear">
                            <input name="end_date" value="{{ $data['end_date'] or '' }}" placeholder="请输入结束日期"
                                   type="text" class="form-control datetimepicker col-sm-2" data-format="YYYY-MM-DD">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <select name="status" class=" form-control">
                            <option value="">请选择状态</option>
                            @foreach(cons()->valueLang('salesman.order.status') as $key => $status)
                                <option value="{{ $key }}" {{ isset($data['status']) && $key == $data['status'] ? 'selected' : '' }} >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="salesman_id" class="form-control">
                            <option value="">请选择业务员</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ isset($data['salesman_id']) && $salesman->id == $data['salesman_id'] ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="submit" class="btn btn-submit btn-default search-by-get"
                           data-url="{{ url('business/order/order-forms') }}">
                </div>
                <table class="table business-table text-center">
                    <tr>
                        <td></td>
                        <td>单号</td>
                        <td>客户名称</td>
                        <td>业务员</td>
                        <td>订单金额</td>
                        <td>时间</td>
                        <td>审核状态</td>
                        <td>操作</td>
                    </tr>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <input type="checkbox" name="order_id[]" value="{{ $order->id }}" class="child">
                            </td>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->salesman_name }}</td>
                            <td>{{ $order->amount }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>{{ cons()->valueLang('salesman.order.status' , $order->status) }}</td>
                            <td>
                                <a class="btn btn-cancel"
                                   href="{{ $order->order_id ?  url('order-sell/detail?order_id=' . $order->order_id)  :url('business/order/' . $order->id) }}">查看</a>
                                @if($order->status == cons('salesman.order.status.not_pass'))
                                    <button
                                            data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                                            data-method="put" data-data='{"status" : "1"}'
                                            class="btn btn-success ajax">通过
                                    </button>
                                @else
                                    <a class="btn btn-primary"
                                       href="{{ url('business/order/export?order_id[]=' . $order->id) }}">导出</a>
                                    @if($order->can_sync)
                                        <button class="btn btn-warning ajax"
                                                data-url="{{ url('api/v1/business/order/' . $order->id . '/sync') }}"
                                                data-method="post">
                                            同步
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="text-right">
                    {!! $orders->render() !!}
                </div>
                <div class="business-operating">
                    <label><input type="checkbox" class="parent">全选</label>
                    <button data-url="{{ url('api/v1/business/order/batch-pass') }}" data-method="put"
                            class="btn batch ajax" type="button">
                        <i class="fa fa-check"></i> 批量通过
                    </button>
                    <button type="submit" class="btn batch ajax"
                            data-url="{{ url('api/v1/business/order/batch-sync') }}"
                            data-method="post">
                        批量同步
                    </button>
                    <button class="btn batch btn-submit" type="submit" data-url="{{ url('business/order/export') }}">
                        批量导出
                    </button>
                </div>
            </form>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('.parent', '.child');
            $('.btn-submit').on('click', function () {
                var obj = $(this);
                obj.closest('form').attr('action', obj.data('url'));
            });
            formSubmitByGet(['order_id[]']);

        })
    </script>
@stop
