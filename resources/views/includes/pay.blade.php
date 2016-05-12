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
                    <div class="operating  pay-way">
                        @foreach(cons()->lang('pay_way.online') as $key=> $way)
                            <label>
                                <input type="radio" {{ $key == 'yeepay' ? 'checked' : '' }} name="pay_way"
                                       value="{{ $key }}" data-way="{{ $key }}"/>
                                <img src="{{ asset('images/' . $key  .'.png') }}"/> &nbsp;&nbsp;&nbsp;
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ url('/') }}" class="btn btn-danger pay" target="_blank">前往支付</a>
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
            var payModal = $('#payModal');
            payModal.on('show.bs.modal', function (e) {
                var payParent = $(e.relatedTarget),
                        orderId = payParent.data('id'),
                        payWayPanel = $('.pay-way'),
                        payWay = payWayPanel.find('input:checked').data('way'),
                        payButton = $('.pay'),
                        payUrl = payButton.attr('href');
                payButton.attr('href', payUrl + '/' + payWay + '/' + orderId);

                payWayPanel.on('change', 'input[name="pay_way"]', function () {
                    var payWay = $(this).data('way'), payUrl = payButton.attr('href');
                    var newPayUrl = payUrl.replace(/\/(\w+)\//, '/' + payWay + '/');
                    payButton.attr('href', newPayUrl);
                })
            });
            payModal.on('hide.bs.modal', function (e) {
                $('.pay').attr('href', '{{ url('/') }}');
            })
        })
    </script>
@stop
