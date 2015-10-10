@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-商家信息')

@section('right')
    @include('index.personal.tabs')
    <form method="get" action="{{ url('personal/balance') }}">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="account-balance">
                    <label>账户余额 :</label>
                    <b class="balance red">￥{{ $balance }}</b>
                    <a class="btn btn-primary">提现</a>
                </div>
                <div class="table-responsive">
                    <label>流水账 :</label>

                    <p class="time">
                        时间段 <input class="datetimepicker" name="start_time" data-format="YYYY-MM-DD" type="text"
                                   value="{{ $startTime }}"> 至
                        <input class="datetimepicker" name="end_time" data-format="YYYY-MM-DD"
                               value="{{ $endTime }}"
                               type="text">
                        <input type="submit" class="btn btn-warning">
                    </p>

                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>交易号</th>
                            <th>订单号</th>
                            <th>详情</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tradeInfo as $trade)
                            <tr>
                                <td>{{ $trade->trade_no }}</td>
                                <td>{{ $trade->order_id }}</td>
                                <td>{{ cons()->valueLang('trade.type' ,$trade->type) }} <b
                                            class="red">￥{{ $trade->amount }}</b></td>
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
        $(function () {
            picFunc();
        })
    </script>
@stop
