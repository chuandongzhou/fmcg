<!-- 弹出层 -->
@section('body')
    <div class="modal fade in" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:500px">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>支付方式</span>
                    </div>
                </div>
                <div class="modal-body text-center">
                    <div class="operating  pay-way text-left">
                        <div class=" title">
                            <div class="item"><span class="prompt">订单号 :</span><b class="order-id"> </b></div>
                            <div class="item"><span class="prompt">应付金额 :</span><b class="red order-price"> </b>
                            </div>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="pay_way" value="balancepay" class="balance-pay">
                                <img class="pay-img" src="{{ asset('images/balance.png') }}">
                            </label>
                            <div class="balance-panel">
                                <span class="prompt">账户余额：</span><b>￥ <span
                                            class="user-balance">{{ $userBalance or 0 }}</span></b>
                                &nbsp;
                                <span class="red balance-not-full hide">当前账户余额不足</span>
                            </div>
                        </div>
                        <br>
                        @foreach(cons()->lang('pay_way.online') as $key=> $way)
                            <label>
                                <input type="radio" {{ $key == 'yeepay' ? 'checked' : '' }} name="pay_way"
                                       value="{{ $key }}"/>
                                <img src="{{ asset('images/' . $key  .'.png') }}"/> &nbsp;&nbsp;&nbsp;
                            </label>
                        @endforeach

                    </div>
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-danger pay" target="_blank">确定支付</a>
                </div>
            </div>
        </div>
    </div>

    @parent
@stop


@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var payModal = $('#payModal'), payWayPanel = payModal.find('.pay-way'), payButton = $('.pay'), payUrl = site.url(), orderIdPanel = $('.order-id'), orderPricePanel = $('.order-price');
            payModal.on('shown.bs.modal', function (e) {
                var payParent = $(e.relatedTarget),
                        orderId = payParent.data('id'),
                        orderPrice = payParent.data('price'),
                        payWay = payWayPanel.find('input:checked').val(),
                        userBalanceControl = payWayPanel.find('.user-balance'),
                        userBalance = parseFloat(userBalanceControl.html());
                orderIdPanel.html(orderId);
                orderPricePanel.html('￥' + orderPrice);
                if (orderPrice > userBalance) {
                    payWayPanel.find('.balance-not-full').removeClass('hide');
                    payWayPanel.find('.balance-pay').prop('disabled', true);
                } else {
                    payWayPanel.find('.balance-not-full').addClass('hide');
                    payWayPanel.find('.balance-pay').prop('disabled', false);
                }

                payButton.attr('href', payUrl + '/' + payWay + '/' + orderId);

                payWayPanel.on('change', 'input[name="pay_way"]', function () {
                    var payWay = $(this).val(), payUrl = payButton.attr('href');
                    var newPayUrl = payUrl.replace(/\/(\w+)\//, '/' + payWay + '/');
                    payButton.attr('href', newPayUrl);
                })
            });
            payModal.on('hide.bs.modal', function (e) {
                payButton.attr('href', payUrl);
                payWayPanel.find('.balanceNotFull').addClass('hide');
                payWayPanel.find('.balance-pay').prop('disabled', false);
            })
        })
    </script>
@stop
