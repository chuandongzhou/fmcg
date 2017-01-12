@extends('admin.master')
@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/feedback') }}">意见反馈</a>
        <a href="javascript:" class="active">支付结果查询</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" method="get" action="{{ url('admin/trade') }}" autocomplete="off">
            <div class="row">
                <div class="col-sm-3">
                    <span>订单号:</span>
                    <input type="text" class="enter-control date" name="order_id"
                           placeholder="请输入订单号" value="{{ array_get($data, 'order_id')}}">
                </div>
                <div class="col-sm-3">
                    <span>交易号:</span>
                    <input type="text" class="enter-control date" name="trade_no" placeholder="请输入交易号" value="{{ array_get($data, 'trade_no')}}">
                </div>
                <div class="col-sm-5">
                    <span>支付平台:</span>
                    <select class="control" name="pay_type">
                        <option value="">全部</option>
                        @foreach (cons()->valueLang('trade.pay_type') as $key=>$value)
                            <option value="{{ $key }}" {{ isset($data['pay_type']) && $key == $data['pay_type'] ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    <input type="submit" class="btn btn-blue control search-by-get" value="查询"/>
                </div>
            </div>
        </form>
        <table class="table public-table table-bordered">
            <tr>
                <th>订单号</th>
                <th>交易号</th>
                <th>类型</th>
                <th>支付平台</th>
                <th>支付结果</th>
                <th>支付金额(元)</th>
                <th>手续费</th>
                <th>交易币种</th>
                <th>交易返回类型</th>
                <th>交易成功时间</th>
            </tr>
            @foreach($trades as $trade)
                <tr>
                    <td>{{ $trade->order_id }}</td>
                    <td>{{ $trade->trade_no  }}</td>
                    <td>{{ cons()->valueLang('trade.type' ,$trade->type) }}</td>
                    <td>{{ cons()->valueLang('trade.pay_type' ,$trade->pay_type) }}</td>
                    <td>{{ cons()->valueLang('trade.pay_status' ,$trade->pay_status) }}</td>
                    <td>{{ $trade->amount }}</td>
                    <td>{{ $trade->target_fee }}</td>
                    <td>{{ cons()->valueLang('trade.trade_currency' ,$trade->trade_currency) }}</td>
                    <td>{{ $trade->callback_type }}</td>
                    <td>{{ $trade->success_at }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="text-right">
        {!! $trades->appends($data)->render() !!}
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>

@stop
