@extends('index.index-master')

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
                    <a href="{{ url('order-buy') }}">查看订单</a>
                    <a href="{{ url('pay?order_id=' . $orderId) }}">点击支付</a>
                </p>
            </div>
        </div>
    </div>

@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $('.pay_type').on('change', function () {
                var obj = $(this), payType = obj.val(), codPayType = obj.parent().next();
                if (payType == 'cod') {
                    codPayType.removeClass('hidden').children('input[type="radio"]').prop('disabled', false);
                } else {
                    codPayType.addClass('hidden').children('input[type="radio"]').prop('disabled', true);
                }
            })
        })
    </script>
@stop
