@extends('admin.master')

@section('right-container')
    <form class="form-horizontal" method="get" action="{{ url('admin/trade') }}">

        <div class="form-group">
            <label for="order_num" class="col-sm-2 control-label">订单号：</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="order_num" name="order_num" placeholder="请输入订单号"
                       value="{{ isset($order_num) ? $order_num : '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="trade_num" class="col-sm-2 control-label">交易号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{ isset($trade_num) ? $trade_num : '' }}" class="form-control" id="trade_num" name="trade_num" placeholder="请输入交易号">
            </div>
        </div>

        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">商家账号：</label>

            <div class="col-sm-4">
                <input type="text" value="{{ isset($account) ? $account : '' }}" class="form-control" id="account" name="account" placeholder="请输入商家账号">
            </div>
        </div>

        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label">支付平台：</label>

            <div class="col-sm-4">
                <input type="radio"  name="pay_type" value="0" checked /> 全部
                @foreach (cons()->valueLang('trade.pay_type') as $key=>$value)
                    <input type="radio" {{ isset($pay_type) && $key == $pay_type ? 'checked' : '' }}  name="pay_type" value="{{ $key }}" /> {{ $value }}
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">添加</button>
            </div>
        </div>
    </form>
@stop