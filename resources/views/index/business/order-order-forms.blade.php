@extends('index.menu-master')
@section('subtitle', '业务管理-订货单')
@include('includes.cropper')

@section('right')
    <div class="col-sm-12 col-xs-9">
        <div class="row">
            <div class="col-xs-12">
                <form class="form-horizontal" data-done-then="refresh" action="{{ url('business/order/export') }}"
                      method="get" autocomplete="off">
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
                                    <a class="btn btn-cancel" href="{{ url('business/order/' . $order->id) }}">查看</a>
                                    @if($order->status == cons('salesman.order.status.not_pass'))
                                        <button
                                                data-url="{{ url('api/v1/business/order/' . $order->id) }}"
                                                data-method="put" data-data='{"status" : "1"}'
                                                class="btn btn-success ajax">通过
                                        </button>
                                    @else
                                        <a class="btn btn-primary"
                                           href="{{ url('business/order/export?order_id[]=' . $order->id) }}">导出</a>
                                        <button class="btn btn-warning ajax"
                                                data-url="{{ url('api/v1/business/order/' . $order->id . '/sync') }}"
                                                data-method="get">
                                            同步
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
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
                        <button class="btn batch" type="submit">批量导出</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('.parent', '.child');
        })
    </script>
@stop
