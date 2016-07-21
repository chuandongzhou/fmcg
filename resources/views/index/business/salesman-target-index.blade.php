@extends('index.menu-master')
@section('subtitle', '业务管理-业务员目标')
@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    业务员目标
@stop

@include('includes.timepicker')
@include('includes.salesman-target-set')

@section('right')
    <form action="{{ url('business/salesman/target') }}" method="get">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div class="col-sm-12 form-group">
                    <span class="item control-item">
                       <input class="inline-control datetimepicker" type="text" name="date" value="{{ $date }}"
                              placeholder="日期"
                              data-format="YYYY-MM">
                    </span>

                    {{--<span class="item control-item">--}}
                    {{--<select name="salesman_id" class="inline-control">--}}
                    {{--<option value="">请选择业务员</option>--}}
                    {{--@foreach($salesmen as $salesman)--}}
                    {{--<option value="{{ $salesman->id }}" {{ $salesmanId == $salesman->id ? 'selected' : '' }}>--}}
                    {{--{{ $salesman->name }}--}}
                    {{--</option>--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    {{--</span>--}}


                    <span class="item control-item">
                        <button type="submit" class="btn btn-default search-by-get">查询</button>
                        <a class="btn btn-default"
                           href="{{ url('api/v1/business/salesman/export-target?date=' . $date) }}">
                            导出
                        </a>
                        <a class="btn btn-default" href="javascript:" data-toggle="modal"
                           data-target="#salesmanTargetSet">
                            设置目标
                        </a>
                    </span>
                </div>

                <div class="col-sm-12 form-group">
                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th>业务员</th>
                            <th>月份目标</th>
                            <th>订货总金额</th>
                            <th>完成率</th>
                            <th>退货总金额</th>
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
                                    {{ $man->orderFormSumAmount }}
                                </td>
                                <td>
                                    {{ $target->getTarget($man->id, $date) ? percentage($man->orderFormSumAmount, $target->getTarget($man->id, $date)) : '100%'}}
                                </td>
                                <td>
                                    {{ $man->returnOrderSumAmount }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
