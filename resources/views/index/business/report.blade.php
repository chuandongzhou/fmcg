@extends('index.manage-master')
@section('subtitle', '业务管理-业务报表')
@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> >
    <span class="second-level"> 业务报表</span>
@stop
@include('includes.timepicker')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level"> 业务报表</span>
                </div>
            </div>
            <form action="{{ url('business/report') }}" method="get">
                <div class="row salesman">
                    <div class="col-sm-12 form-group salesman-controls">
                        <form action="">
                            <input class="inline-control datetimepicker control" type="text" name="start_date"
                                   value="{{ $startDate }}"
                                   data-format="YYYY-MM-DD" placeholder="开始时间">至
                            <input class="inline-control datetimepicker control" type="text" name="end_date"
                                   value="{{ $endDate }}"
                                   data-format="YYYY-MM-DD" placeholder="结束时间">
                            <input class="inline-control control" type="text" name="salesman_name"
                                   value="{{ $salesmanName or '' }}" placeholder="业务员名称">
                            <button id="submitBtn" class="btn btn-blue-lighter search-by-get" type="submit">查询</button>
                            <a id="export"
                               href="{{ url('business/report/export?start_date=' . $startDate . '&end_date=' . $endDate. '&salesman_name=' . $salesmanName) }}"
                               class="btn btn-border-blue">导出</a>
                        </form>
                    </div>
                    <div class="col-sm-12 form-group">

                        <table class="table table-bordered table-center table-middle public-table">
                            <thead>
                            <tr>
                                <th>业务员</th>
                                <th>拜访客户数</th>
                                <th>订货单数(拜访+自主)</th>
                                <th>订货总金额(拜访+自主)</th>
                                <th>已配送单数</th>
                                <th>已完成金额</th>
                                <th>未完成金额</th>
                                <th>退货单数</th>
                                <th>退货总金额</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($salesmen as $man)
                                <tr>
                                    <td>{{ $man->name }}</td>
                                    <td>{{ $man->visitCustomerCount }}</td>
                                    <td>
                                        <b class="red">{{ $man->orderFormCount }}</b>
                                        ({{ $man->visitOrderFormCount . '+' . bcsub($man->orderFormCount, $man->visitOrderFormCount) }}
                                        )
                                    </td>
                                    <td>
                                        <b class="red">{{ $man->orderFormSumAmount }}</b>
                                        ({{ $man->visitOrderFormSumAmount . '+' . ($man->orderFormSumAmount - $man->visitOrderFormSumAmount) }}
                                        )
                                    </td>
                                    <td>{{ $man->deliveryFinishCount }}</td>
                                    <td>{{ $man->finishedAmount }}</td>
                                    <td>{{ $man->notFinishedAmount }}</td>
                                    <td>{{ $man->returnOrderCount }}</td>
                                    <td><b class="red">{{ $man->returnOrderSumAmount }}</b></td>
                                    <td>
                                        <a href="{{ url('business/report/' . $man->id . "?start_date={$startDate}&end_date={$endDate}") }}"
                                           class="edit"><i class="iconfont icon-iconmingchengpaixu65"></i>明细</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
    </script>
@stop
