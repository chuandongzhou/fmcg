@extends('index.manage-master')
@section('subtitle', '业务管理-业务员目标')
@include('includes.timepicker')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level">业务员目标</span>
                </div>
            </div>
            <form action="{{ url('business/salesman/target') }}" method="get">
                <div class="row salesman">
                    <div class="col-sm-12 form-group salesman-controls">
                        <form action="" method="get" autocomplete="off">
                            <input class="inline-control datetimepicker control" type="text" name="date"
                                   value="{{ $date }}"
                                   placeholder="日期"
                                   data-format="YYYY-MM">
                            <button type="submit" class=" btn btn-blue-lighter search control search-by-get">查询</button>
                            <a type="button" href="{{ url('api/v1/business/salesman/export-target?date=' . $date) }}"
                               class="btn btn-border-blue control statistical">导出</a>
                            <a class=" btn btn-blue-lighter control" href="{{ url('business/salesman/target-set') }}">设置目标</a>
                        </form>
                    </div>

                    <div class="col-sm-12 table-responsive table-wrap">
                        <table class="table-bordered table table-center public-table">
                            <thead>
                            <tr align="center">
                                <th>业务员</th>
                                <th>月份目标（元）</th>
                                <th>完成金额（元）</th>
                                <th>完成率</th>
                                <th>退货总额（元）</th>
                                <th>成交家数</th>
                                <th>新开点（家）</th>
                                <th>单品目标</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($salesmen as $man)
                                <tr>
                                    <td>
                                        {{ $man->name }}
                                    </td>
                                    <td>
                                        {{ $target->getTarget($man->id, $date) }}
                                    </td>
                                    <td>
                                        {{ $man->finishedAmount }}
                                    </td>
                                    <td>
                                        {{ $target->getTarget($man->id, $date) ? percentage($man->finishedAmount, $target->getTarget($man->id, $date)) : '100%'}}
                                    </td>
                                    <td>
                                        {{ $man->returnOrderSumAmount }}
                                    </td>
                                    <td>{{ $man->finishedCount }}</td>
                                    <td>{{ $man->newCustomers }}</td>
                                    <td><a class="edit" href="javascript:;" data-id="{{ $man->id }}"
                                           data-month="{{ $date }}"
                                           data-target="#salesmanGoodsTarget" data-toggle="modal">查看</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>

                </div>
            </form>
        </div>
    </div>

    @include('includes.salesman-goods-target')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop
