@extends('mobile.master')

@section('subtitle', '订单支付')

@section('header')
    <div class="fixed-header fixed-item shopping-nav">
        <div class="row nav-top margin-clear white-bg">
            <div class="col-xs-2 edit-btn pd-clear">
                <a class="iconfont icon-fanhui2 go-back"></a>
            </div>
            <div class="col-xs-10  pd-right-clear">
                订单支付
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <form class="mobile-ajax-form" method="get" action="{{ url('V1' . $order->id) }}">
        <div class="container-fluid  m60 p65 pay-wrap">
            <div class="row">
                <div class="col-xs-12 clearfix item">
                    <div class="pull-left">订单号</div>
                    <div class="pull-right">{{ $order->id }}</div>
                </div>
                <div class="col-xs-12 clearfix item">
                    <div class="pull-left">应付金额</div>
                    <div class="pull-right red amount" data-amount="{{ $order->after_rebates_price }}">
                        ¥{{ $order->after_rebates_price }}</div>
                </div>
                <div class="col-xs-12 clearfix item">
                    <div class="pull-left">账户可用金额</div>
                    <div class="pull-right balance" data-balance="{{ $user->available_balance }}">
                        ¥{{ $user->available_balance }}</div>
                </div>
                <div class="col-xs-12 item pay-type-warp">
                    <div class="prompt">选择支付方式</div>
                    <div class="pay-type-item">
                        <label>
                            <input type="radio" name="channel" value="balancepay" class="balance-pay">
                            <img src="{{ asset('images/mobile-images/logo_balancepay.png') }}">
                        </label>
                    </div>
                    <div class="pay-type-item">
                        <label>
                            <input type="radio" name="channel" value="yeepay_wap" checked>
                            <img src="{{ asset('images/mobile-images/logo_yeepay.png') }}">
                        </label>
                    </div>

                    <div class="pay-type-item">
                        <label>
                            <input type="radio" name="channel" value="alipay_wap">
                            <img src="{{ asset('images/mobile-images/logo_alipay.png') }}">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item pay-footer white-bg">
            <div class="item prompt">订单已提交，请于24小时内完成支付</div>
            <div class="item">
                <button type="submit" data-done-then="none" data-no-prompt="true" class="btn btn-danger submit pay"
                        data-data='{"web" : "true"}'>确定支付
                </button>
            </div>
        </div>
    </form>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/pingpp.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var payButton = $('.pay'), payWayPanel = $('.pay-type-warp'), payUrl = site.url();

            var orderPrice = $('.amount').data('amount'),
                userBalance = parseFloat($('.balance').data('balance'));

            if (orderPrice > userBalance) {
                payWayPanel.find('.balance-pay').prop('disabled', true);
            } else {
                payWayPanel.find('.balance-pay').prop('disabled', false);
            }

            $('.mobile-ajax-form').on('done.hct.ajax', function (obj, charge) {
                if (payWayPanel.find('input[name="channel"]:checked').val() !== 'balancepay') {
                    pingpp.createPayment(charge, function (result, err) {
                        if (result === "success") {
                            // 只有微信公众账号 wx_pub 支付成功的结果会在这里返回，其他的支付结果都会跳转到 extra 中对应的 URL。
                        } else if (result === "fail") {
                            showMassage(err.msg);
                            // charge 不正确或者微信公众账号支付失败时会在此处返回
                        } else if (result === "cancel") {
                            // 微信公众账号支付取消支付
                        }
                    });
                }
            });

            payWayPanel.on('change', 'input[name="channel"]', function () {
                if ($(this).val() === 'balancepay') {
                    payButton.data('url', site.api('pay/balancepay/{{ $order->id }}'));
                    payButton.data('doneUrl', site.url('order'));
                    payButton.data('method', 'post');
                    payButton.data('noPrompt', '');
                    payButton.data('doneText', '支付成功');
                } else {
                    payButton.data('url', null);
                    payButton.data('doneUrl', null);
                    payButton.data('doneThen', 'none');
                    payButton.data('method', null);
                    payButton.data('noPrompt', 'true');
                    payButton.data('doneText', null);
                }
            })
        })
    </script>
@stop
