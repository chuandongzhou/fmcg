@extends('index.index-master')

@section('subtitle' , '提交成功')

@section('container')
    <div class="container dealer-index index shopping-cart">
        <div class="row audit-step-outs">
            <div class="col-sm-3 step ">
                1.查看购物车
                <span></span>
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step ">
                2.确认订单消息
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step step-active">
                3.成功提交订单
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step">
                4.等待确认
            </div>
        </div>
        <div class="row table-list-row">
            <div class="col-sm-12 shopping-finish text-center">
                <i class="fa fa-check-circle-o order-ok-icon"></i>

                <p class="order-ok-title">订单已提交，请于24小时内完成支付</p>

                <p class="finish-operating">
                    <a href="{{ url('pay/request/' . $orderId . ($type == 'all' ? '?type=all' : '')) }}" class="btn btn-danger pay">前往支付</a>
                    <a href="{{ url('order-buy') }}" class="check-order" >查看订单</a>
                </p>
            </div>
        </div>
    </div>
@stop