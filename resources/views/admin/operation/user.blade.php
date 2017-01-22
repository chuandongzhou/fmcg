@extends('admin.master')

@section('subtitle' , '用户数据统计')

@include('includes.timepicker')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="#" class="active">用户数据统计</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/operation-data/user') }}" method="get" autocomplete="off">
            <input type="text" name="begin_day" class="datetimepicker control text-center" data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}"/>
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="datetimepicker control text-center" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}"/>

            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="javascript:" class="btn btn-border-blue control export">导出</a>
        </form>
        <div id="myTabContent" class="tab-content">
            <table class="table public-table table-bordered">
                <tr>
                    <th>名称</th>
                    <th>供应商</th>
                    <th>批发商</th>
                    <th>终端商</th>
                    <th>总计</th>
                </tr>
                <tr>
                    <td>注册数</td>
                    <td>{{ $supplierRegNum = $dataStatistics->sum('supplier_reg_num') }}</td>
                    <td>{{ $wholesalerRegNum = $dataStatistics->sum('wholesaler_reg_num') }}</td>
                    <td>{{ $retailerRegNum = $dataStatistics->sum('retailer_reg_num') }}</td>
                    <td>{{ $supplierRegNum + $wholesalerRegNum + $retailerRegNum }}</td>
                </tr>
                <tr>
                    <td>
                        <a class="iconfont icon-tixing " data-container="body" data-toggle="popover"
                           data-placement="bottom"
                           data-content="最近30天登录为活跃用户，反之则为不活跃用户">
                        </a>
                        活跃用户数
                    </td>
                    <td>{{ $activeUser ? $activeUser->active_user[0] .'(' . $activeUser->created_at . ')' : 0 }}</td>
                    <td>{{ $activeUser ? $activeUser->active_user[1] .'(' . $activeUser->created_at . ')'  : 0 }}</td>
                    <td>{{ $activeUser ? $activeUser->active_user[2] .'(' . $activeUser->created_at . ')'  : 0 }}</td>
                    <td>{{ $activeUser ? array_sum($activeUser->active_user) : 0 }}</td>
                </tr>
                <tr>
                    <td>下单用户数</td>
                    <td> - - </td>
                    <td> {{ $orderGroup['wholesalerOrders']->pluck('user_id')->toBase()->unique()->count() }}</td>
                    <td> {{ $orderGroup['retailerOrders']->pluck('user_id')->toBase()->unique()->count() }}</td>
                    <td> - - </td>
                </tr>

                <tr>
                    <td>成单用户数</td>
                    <td> - - </td>
                    <td>{{ $completeOrderGroup['wholesalerOrders']->pluck('user_id')->toBase()->unique()->count() }}</td>
                    <td>{{ $completeOrderGroup['retailerOrders']->pluck('user_id')->toBase()->unique()->count() }}</td>
                    <td> - - </td>
                </tr>
            </table>
        </div>
        <div id="myChart" class="chart"></div>
        <div id="myChart1" class="chart"></div>
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
        userData();
    </script>
@stop