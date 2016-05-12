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
            <div class="col-sm-3 step">
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
            <div class="col-sm-4 col-sm-offset-4 shopping-finish text-center">
                <i class="fa fa-check-circle-o order-ok-icon"></i>

                <p class="order-ok-title">订单已提交，请于24小时内完成支付</p>

                <div class="operating  pay-way">
                    <p class="text-left title">请选择支付方式：</p>
                    @foreach(cons()->lang('pay_way.online') as $key=> $way)
                        <label>
                            <input type="radio" {{ $key == 'yeepay' ? 'checked' : '' }} name="pay_way"
                                   value="{{ $key }}" data-way="{{ $key }}"/>
                            <img src="{{ asset('images/' . $key  .'.png') }}"/> &nbsp;&nbsp;&nbsp;
                        </label>
                    @endforeach
                </div>

                <p class="finish-operating">
                    <a href="{{ url('yeepay/' . $orderId . ($type == 'all' ? '?type=all' : '')) }}"
                       class="btn btn-danger pay" onclick="showPaySuccess()" target="_blank">前往支付</a>
                    <a href="{{ url('order-buy') }}" class="check-order">查看订单</a>
                </p>
            </div>
        </div>
    </div>
    @include('includes.pay-success')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('.pay-way').on('change', 'input[name="pay_way"]', function () {
            var payWay = $(this).data('way'), pay = $('.pay'), payUrl = pay.attr('href');
            var newPayUrl = payUrl.replace(/\/(\w+)\//, '/' + payWay + '/');
            pay.attr('href', newPayUrl);
        })
    </script>
@stop
