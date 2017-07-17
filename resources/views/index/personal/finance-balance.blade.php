@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '财务管理-账户余额')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/finance/balance') }}">财务管理</a> >
                    <span class="second-level"> 账户余额</span>
                </div>
            </div>
            <form method="get" action="{{ url('personal/finance/balance') }}" autocomplete="off">
                <div class="row balance">
                    <div class="col-sm-12 ">
                        @include('index.personal.finance-common')
                        <div class="table">
                            <div class="form-group">
                                <input class="datetimepicker inline-control control" name="start_time"
                                       data-format="YYYY-MM-DD"
                                       type="text"
                                       value="{{ $data['start_time'] or '' }}"> 至
                                <input class="datetimepicker inline-control control" name="end_time"
                                       data-format="YYYY-MM-DD"
                                       value="{{ $data['end_time'] or '' }}"
                                       type="text">
                                <input type="submit" value="提交" class="btn btn-blue-lighter search-by-get">
                            </div>
                            <div class="time form-group">
                                <table class="table table-bordered table-center public-table">
                                    <thead>
                                    <tr>
                                        <th>订单号</th>
                                        <th>实收金额</th>
                                        <th>手续费</th>
                                        <th>支付平台</th>
                                        <th>交易号</th>
                                        <th>完成时间</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tradeInfo as $trade)
                                        <tr>
                                            <td>{{ $trade->order_id }}</td>
                                            <td><b class="red">¥{{ $trade->amount }}</b></td>
                                            <td>{{ $trade->target_fee }}</td>
                                            <td>{{ cons()->valueLang('trade.pay_type' , $trade->pay_type) }}</td>
                                            <td>{{ $trade->trade_no }}</td>
                                            <td>{{ $trade->finished_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                {!! $tradeInfo->appends($data)->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @include('includes.withdraw')
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            picFunc();
            formSubmitByGet();
            $("[data-toggle='popover']").popover();
        });
    </script>
@stop