<!-- 弹出层 -->
@section('body')
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">支付方式</h4>
                </div>
                <div class="modal-body text-center">
                    <div class="operating  pay-way text-left">
                        <p class="text-left title">
                            当前账户余额：<span class="red">¥<b class="user-balance">{{ $userBalance or 0 }}</b> &nbsp;</span>
                            <span class="red balance-not-full hide">(余额不足)</span>
                        </p>
                        <label>
                            <input type="radio" name="pay_way" value="balancepay" class="balance-pay">
                            <img class="pay-img" src="{{ asset('images/balance.png') }}">
                        </label>
                        <br/> <br/>
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
                    <a href="" class="btn btn-danger pay" target="_blank">前往支付</a>
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
            var payModal = $('#payModal'), payWayPanel = payModal.find('.pay-way'), payButton = $('.pay'), payUrl = site.url();
            payModal.on('shown.bs.modal', function (e) {
                var payParent = $(e.relatedTarget),
                        orderId = payParent.data('id'),
                        orderPrice = payParent.data('price'),
                        payWay = payWayPanel.find('input:checked').val(),
                        userBalanceControl = payWayPanel.find('.user-balance'),
                        userBalance = parseFloat(userBalanceControl.html());
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
