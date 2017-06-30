@extends('mobile.master')

@section('subtitle', '订单提交成功')

@section('body')
    @parent
    <div class="container-fluid">
        <div class="row order-success-wrap">
            <div class="col-xs-12 content text-center">
                <i class="iconfont icon-wancheng" ></i>
                <div class="txt">恭喜！订单提交成功，请等待商户确认</div>
            </div>
            <div class="col-xs-12 btn-panel">
                <a href="{{ url('order') }}" class="btn btn-primary">查看订单</a>
            </div>
        </div>
    </div>
@stop
