@extends('index.index-master')

@section('subtitle' , '提交成功')

@section('container')
    <div class="container dealer-index index shopping-cart">
        <div class="row table-list-row">
            <div class="col-sm-4 col-sm-offset-4 shopping-finish">
                <div class="order-ok-title pay-ok"><i class="fa fa-check-circle-o order-ok-icon"></i> 支付成功 <a
                            href="{{ url('order-buy') }}" class="check-order">查看订单</a></div>
            </div>
        </div>
    </div>
@stop

