@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-商家信息')

@section('right')
    <form method="get" action="{{ url('personal/balance') }}" autocomplete="off" class="form-horizontal">
        <div class="row">
            <div class="col-sm-12 ">
                <div class="account-balance">
                    <label>账户余额 :</label>
                    <b class="balance red">￥{{ $balance }}</b>
                    <a class="btn btn-primary" data-target="#withdraw" data-toggle="modal">提现</a>
                </div>
                <div class="personal-center">
                    <div class=" switching">
                        <a href="{{ url('personal/balance') }}" class="btn active">流水账</a>
                        <a href="{{ url('personal/withdraw') }}" class="btn">提现记录</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <div class="time form-group">
                        时间段： <input class="datetimepicker inline-control" name="start_time" data-format="YYYY-MM-DD"
                                    type="text"
                                    value="{{ $data['start_time'] }}"> 至
                        <input class="datetimepicker inline-control" name="end_time" data-format="YYYY-MM-DD"
                               value="{{ $data['end_time'] }}"
                               type="text">
                        <input type="submit" class="btn btn-default search-by-get">
                    </div>
                    <div class="time form-group">
                        <table class="table table-bordered table-center">
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
                                    <td><b class="red">￥{{ $trade->amount }}</b></td>
                                    <td>{{ $trade->target_fee }}</td>
                                    <td>{{ cons()->valueLang('trade.pay_type')[$trade->pay_type] }}</td>
                                    <td>{{ $trade->trade_no }}</td>
                                    <td>{{ $trade->finished_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $tradeInfo->appends($data)->render() !!}
                </div>
            </div>
        </div>
    </form>
    @include('includes.withdraw')
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            picFunc();
            getWithdraw({{ $balance }});
            formSubmitByGet();
        });
    </script>
@stop
