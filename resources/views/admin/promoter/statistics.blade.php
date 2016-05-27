@extends('admin.master')
@section('subtitle' , '业务员数据统计')
@include('includes.timepicker')
@section('right-container')
    <form class="form-horizontal" action="{{ url('admin/promoter/statistics') }}" method="get" autocomplete="off">
        <div class="form-group editor-item">
            <label class="control-label col-sm-1">月份 :</label>

            <div class="col-sm-2">
                <input type="text" name="month" class="month form-control datetimepicker"
                       data-format="YYYY-MM" value="{{ $month }}"/>
            </div>
            <label class="control-label col-sm-1">业务员 :</label>

            <div class="col-sm-2">
                <select name="id" class="form-control">
                    <option value="">请选择业务员</option>
                    @foreach($allPromoters as $key=> $promoter)
                        <option value="{{ $key }}" {{ $key == $promoterId ? 'selected' : '' }}>{{ $promoter }}</option>
                    @endforeach
                </select>
            </div>
            <input type="submit" class="btn btn-default search-by-get" value="查询"/>
        </div>

    </form>

    <div id="myTabContent" class="tab-content">
        @foreach($promoters as $promoter)
            <table class="table table-bordered">
                <tr align="center">
                    <td rowspan="3" width="12%" style="vertical-align: middle">{{ $promoter->name }}</td>
                    <td width="15%">新注册数</td>
                    <td width="5%">{{ $promoter->shopRegisterCount }}</td>
                    <td width="15%">下单数</td>
                    <td width="10%">{{ $promoter->submitOrdersCount }}</td>
                    <td width="10%">成单数</td>
                    <td>{{ $promoter->finishedOrdersCount }}
                        ({{ $promoter->currentMonthFinishedOrdersCount }}
                        + {{ bcsub($promoter->finishedOrdersCount, $promoter->currentMonthFinishedOrdersCount , 2); }})
                    </td>
                </tr>
                <tr align="center">
                    <td>总用户数</td>
                    <td>{{ $promoter->shops->count() }}</td>
                    <td>下单总金额</td>
                    <td>{{ $promoter->submitOrdersAmount }}</td>
                    <td>成单总金额</td>
                    <td>
                        {{ $promoter->finishedOrdersAmount }}
                        （{{ $promoter->currentMonthFinishedOrdersAmount }}
                        + {{ bcsub ($promoter->finishedOrdersAmount,  $promoter->currentMonthFinishedOrdersAmount , 2) }}
                        ）
                    </td>
                </tr>
                <tr align="center">
                    <td>下单用户数</td>
                    <td>{{ $promoter->submitOrdersUsersCount }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        @endforeach
    </div>
@stop
@section('js')
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop

