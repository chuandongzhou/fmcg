@extends('admin.master')

@section('subtitle' , '金额数据统计')

@include('includes.timepicker')

@section('right-container')
    @include('admin.operation.financial-nav')
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/operation-data/financial') }}" method="get"
              autocomplete="off">
            <a href="{{ url('admin/operation-data/financial?t=today') }}" class="time-format {{ array_get($data, 't') == 'today' ? 'active' : '' }}">今天</a>
            <a href="{{ url('admin/operation-data/financial?t=yesterday') }}" class="time-format {{ array_get($data, 't') == 'yesterday' ? 'active' : '' }}">昨天</a>
            <a href="{{ url('admin/operation-data/financial?t=week') }}" class="time-format {{ array_get($data, 't') == 'week' ? 'active' : '' }}">本周</a>
            <a href="{{ url('admin/operation-data/financial?t=month') }}" class="time-format {{ array_get($data, 't') == 'month' ? 'active' : '' }}">本月</a>
            <input type="text" name="begin_day" class="datetimepicker enter-control text-center" data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}"/>
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="datetimepicker enter-control text-center" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}"/>
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="{{ url('admin/operation-data/financial-export?' . http_build_query($data)) }}" class="btn btn-border-blue control export">导出</a>
        </form>
        <div id="myTabContent" class="tab-content">
            <table class="table public-table table-bordered">
                <tr>
                    <th width="25%">名称</th>
                    <th width="25%">终端商</th>
                    <th width="25%">批发商</th>
                    <th width="25%">总计</th>
                </tr>
                <tr>
                    <td>下单笔数</td>
                    <td>{{ $retailer['orderCount'] }}</td>
                    <td>{{ $wholesaler['orderCount'] }}</td>
                    <td>{{ bcadd($retailer['orderCount'], $wholesaler['orderCount']) }}</td>
                </tr>
                <tr>
                    <td>
                        下单金额
                        <a class="iconfont icon-tixing " data-container="body" data-toggle="popover" data-placement="bottom" data-content="包括陈列费金额和优惠券金额  下单金额 = 线上支付总金额 + 线下支付总金额"></a>
                    </td>
                    <td>￥{{ number_format($retailer['orderAmount'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderAmount'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderAmount'], $wholesaler['orderAmount'], 2), 2) }}</td>
                </tr>
                <tr>
                    <td>
                        线上总金额
                    </td>
                    <td>￥{{ number_format($retailer['orderPaidByOnline'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderPaidByOnline'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderPaidByOnline'], $wholesaler['orderPaidByOnline'], 2), 2) }}</td>
                </tr>
                <tr>
                    <td>
                        线上完成总额
                    </td>
                    <td>￥{{ number_format($retailer['orderCompleteByOnline'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderCompleteByOnline'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderCompleteByOnline'], $wholesaler['orderCompleteByOnline'], 2), 2) }}</td>
                </tr>
                <tr>
                    <td>
                        线下总金额
                    </td>
                    <td>￥{{ number_format($retailer['orderPaidByOffline'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderPaidByOffline'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderPaidByOffline'], $wholesaler['orderPaidByOffline'], 2), 2) }}</td>
                </tr>
                <tr>
                    <td>
                        线下完成总额
                    </td>
                    <td>￥{{ number_format($retailer['orderCompleteByOffline'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderCompleteByOffline'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderCompleteByOffline'], $wholesaler['orderCompleteByOffline'], 2), 2) }}</td>
                </tr>
                <tr>
                    <td>
                        线下POS机完成总额
                    </td>
                    <td>￥{{ number_format($retailer['orderCompleteByPos'], 2) }}</td>
                    <td>￥{{ number_format($wholesaler['orderCompleteByPos'], 2) }}</td>
                    <td>￥{{ number_format(bcadd($retailer['orderCompleteByPos'], $wholesaler['orderCompleteByPos'], 2), 2) }}</td>
                </tr>
            </table>
        </div>
        <div class="chart-wrap">
            <div id="myChart" class="chart"></div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        financial();
        $("[data-toggle='popover']").popover();
    </script>
@stop