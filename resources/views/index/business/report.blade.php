@extends('index.menu-master')
@section('subtitle', '业务管理-业务员目标')
@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    业务报告
@stop


@include('includes.timepicker')

@section('right')
    <form action="{{ url('business/report') }}" method="get">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div class="col-sm-12 form-group">
                    <span class="item control-item">
                        开始日期 ：
                       <input class="inline-control datetimepicker" type="text" name="start_date"
                              value="{{ $startDate }}"
                              data-format="YYYY-MM-DD">
                    </span>
                    <span class="item control-item">
                        结束日期 ：
                       <input class="inline-control datetimepicker" type="text" name="end_date" value="{{ $endDate }}"
                              data-format="YYYY-MM-DD">
                    </span>


                    <span class="item control-item">
                        <button type="submit" class="btn btn-default search-by-get">查询</button>
                        <a class="btn btn-default"
                           href="{{ url('business/report/export?start_date=' . $startDate . '&end_date=' . $endDate) }}">
                            导出
                        </a>
                    </span>
                </div>

                <div class="col-sm-12 form-group">
                    @foreach($salesmen as $man)
                        <table class="table table-bordered table-center table-middle">

                            <tr>
                                <th rowspan="2" width="20%">{{ $man->name }}</th>
                                <th>拜访客户数</th>
                                <th>订货单数</th>
                                <th>订货总金额</th>
                                <th>退货单数</th>
                                <th>退货总金额</th>
                                <th>操作</th>
                            </tr>
                            <tr>
                                <td>
                                    {{ $man->visitCustomerCount }}
                                </td>
                                <td>
                                    {{ $man->orderFormCount }}
                                </td>
                                <td>
                                    {{ $man->orderFormSumAmount }}
                                </td>
                                <td>
                                    {{ $man->returnOrderCount }}
                                </td>
                                <td>
                                    {{ $man->returnOrderSumAmount }}
                                </td>
                                <td>
                                    <a href="{{ url('business/report/' . $man->id . "?start_date={$startDate}&end_date={$endDate}") }}">明细</a>
                                </td>
                            </tr>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
    </script>
@stop
