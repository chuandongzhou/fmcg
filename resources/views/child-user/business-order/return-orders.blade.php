@extends('child-user.manage-master')
@section('subtitle', '业务管理-退货单')
@include('includes.timepicker')

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/salesman') }}">业务管理</a> >
                    <span class="second-level">退货单</span>
                </div>
            </div>
            <form action="{{ url('child-user/business-order/return-orders') }}" class="form-horizontal ajax-form"
                  data-done-then="refresh"
                  autocomplete="off">
                <div class="row delivery">
                    <div class="col-sm-12 control-search">

                        <input class="enter control datetimepicker" name="start_date"
                               value="{{ $data['start_date'] or '' }}" placeholder="请输入开始日期" type="text"
                               data-format="YYYY-MM-DD"
                        >至
                        <input class="enter control datetimepicker"
                               type="text" data-format="YYYY-MM-DD"
                               name="end_date" value="{{ $data['end_date'] or '' }}" placeholder="请输入结束日期">
                        <select name="status" class="control">
                            <option value="">请选择状态</option>
                            @foreach(cons()->valueLang('salesman.order.status') as $key => $status)
                                <option value="{{ $key }}" {{ isset($data['status']) && $key == $data['status'] ? 'selected' : '' }} >{{ $status }}</option>
                            @endforeach
                        </select>
                        <select name="salesman_id" class="control">
                            <option value="">请选择业务员</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ isset($data['salesman_id']) && $salesman->id == $data['salesman_id'] ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="control enter" name="customer" placeholder="请输入单号/客户名称"
                               aria-describedby="course-search" value="{{ $data['customer'] or '' }}">

                        <button type="submit"
                                class=" btn btn-blue-lighter search control search-by-get btn-submit">提交
                        </button>

                    </div>
                    <div class="col-sm-12 table-responsive table-wrap">
                        <table class="table-bordered table table-center public-table">
                            <thead>
                            <tr align="center">
                                <th>选择</th>
                                <th>单号</th>
                                <th>客户名称</th>
                                <th>业务员</th>
                                <th>退货金额</th>
                                <th>时间</th>
                                <th>审核状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="order_id[]" value="{{ $order->id }}" class="child">
                                    </td>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->salesman_name }}</td>
                                    <td>{{ $order->amount }}</td>
                                    <td>{{ $order->updated_at }}</td>
                                    <td>{{ cons()->valueLang('salesman.order.status' , $order->status) }}</td>
                                    <td>

                                        <a class=" color-blue" href="{{ url('child-user/business-order/' . $order->id) }}">
                                            <i class="iconfont icon-iconmingchengpaixu65"></i>查看
                                        </a>
                                        @if($order->status == cons('salesman.order.status.not_pass'))
                                            <a data-url="{{ url('api/v1/child-user/business-order/' . $order->id) }}"
                                               data-method="put" data-data='{"status" : "1"}'
                                               class=" ajax">
                                                <i class="iconfont  icon-tongguo"></i>通过
                                            </a>
                                            <a data-url="{{ url('api/v1/child-user/business-order/' . $order->id) }}"
                                               data-method="delete"
                                               class="red ajax" type="button">
                                                <i class="iconfont icon-shanchu"></i>删除</a>
                                        @else
                                            <a class="orange" target="_blank"
                                               href="{{ url('child-user/business-order/browser-export/' . $order->id) }}">
                                                <i class="iconfont  icon-dayin"></i>打印</a>
                                            {{--@if($order->can_sync)--}}
                                            {{--<button class="btn btn-warning ajax"--}}
                                            {{--data-url="{{ url('api/v1/child-user/business-order/' . $order->id . '/sync') }}"--}}
                                            {{--data-method="post">--}}
                                            {{--同步--}}
                                            {{--</button>--}}
                                            {{--@endif--}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="business-operating">
                            <label><input type="checkbox" class="parent">全选</label>
                            <button data-url="{{ url('api/v1/child-user/business-order/batch-pass') }}" data-method="put"
                                    class="btn btn-primary batch ajax" type="button">
                                <i class="fa fa-check"></i> 批量通过
                            </button>
                        </div>
                        <div class="text-right">
                            {!! $orders->appends($data)->render() !!}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('.parent', '.child');
            formSubmitByGet(['order_id[]']);
        })
    </script>
@stop
