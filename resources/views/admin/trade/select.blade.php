@extends('admin.master')
@section('right-container')
    <form class="form-horizontal" method="get" action="{{ url('admin/trade') }}" autocomplete="off">

        <div class="form-group">
            <label for="order_num" class="col-sm-2 control-label">订单号：</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="order_num" name="order_num" placeholder="请输入订单号"
                       value="{{ isset($order_id) ? $order_id : '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="trade_no" class="col-sm-2 control-label">交易号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{ isset($trade_no) ? $trade_no : '' }}" class="form-control" id="trade_no"
                       name="trade_no" placeholder="请输入交易号">
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label">支付平台：</label>

            <div class="col-sm-4 pay_type">
                <input type="radio" name="pay_type" value="0" checked/> 全部 &nbsp;&nbsp;&nbsp;
                @foreach (cons()->valueLang('trade.pay_type') as $key=>$value)
                    <input type="radio" {{ isset($pay_type) && $key == $pay_type ? 'checked' : '' }}  name="pay_type"
                           value="{{ $key }}"/> {{ $value }} &nbsp;&nbsp;&nbsp;
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary search-by-get">查询</button>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>类型</th>
                <th>支付平台</th>
                <th>商家账号</th>
                <th>订单号</th>
                <th>交易号</th>
                <th>支付结果</th>
                <th>支付金额</th>
                <th>交易币种</th>
                <th>交易返回类型</th>
                <th>交易成功时间</th>
                <th>交易结果通知</th>
                <th>hamc</th>
            </tr>
            </thead>
            <tbody>
            @foreach($trades as $trade)
                <tr>
                    <td>{{ cons()->valueLang('trade.type' ,$trade->type) }}</td>
                    <td>{{ cons()->valueLang('trade.pay_type' ,$trade->pay_type) }}</td>
                    <td>{{ $trade->account }}</td>
                    <td>{{ $trade->order_num }}</td>
                    <td>{{ $trade->trade_no }}</td>
                    <td>{{ cons()->valueLang('trade.pay_status' ,$trade->pay_status) }}</td>
                    <td>{{ $trade->amount }}</td>
                    <td>{{ cons()->valueLang('trade.trade_currency' ,$trade->trade_currency) }}</td>
                    <td>{{ $trade->callback_type }}</td>
                    <td>{{ $trade->success_at }}</td>
                    <td>{{ $trade->notice_at }}</td>
                    <td>{{ $trade->hmac }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
    @if(!empty($trades))
        {!! $trades->render() !!}
    @endif
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>

@stop
