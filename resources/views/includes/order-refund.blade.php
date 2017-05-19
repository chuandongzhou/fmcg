<!-- 弹出层 -->
@section('body')
    <div class="modal fade in" id="refund" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <form class="form-horizontal ajax-form" method="post" data-help-class="col-sm-push-2 col-sm-10"
                      autocomplete="off">
                    <input type="hidden" name="_method" value="put">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="refundModalLabel">
                            订单退款 <span class="extra-text"></span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="operating  refund-wrap text-left">
                            <div class="title text-center">
                                退款手续费由买家承担
                            </div>
                            <div class="refund-content">
                                网银支付手续费扣支付总额的0.3%，一键支付手续费扣支付总额的0.6%，扫码支付手续费扣支付总额的0.7%，手续费在退款成功的同时扣除，系统将在七个工作日内返还至您所选择支付方式的账户。
                            </div>
                        </div>
                        <div class="modal-title form-group" id="cropperModalLabel">
                            <label class="col-sm-2 text-right control-label">退款原因:</label>

                            <div class="extra-text col-sm-8">
                                <textarea class="form-control" rows="4" name="reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel btn-sm btn-close" data-dismiss="modal">取消退款</button>
                        <button type="submit" class="btn btn-red pay" data-text="确定退款">确定退款</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var payModal = $('#refund'), submitBtn = payModal.find('button[type="submit"]');
            payModal.on('show.bs.modal', function (e) {
                var payParent = $(e.relatedTarget),
                        url = payParent.data('url'),
                        seller = payParent.data('seller');
                submitBtn.data('url', url);
                if (seller) {
                    submitBtn.attr('data-data', '{"is_seller" : true}');
                }
            });
        })
    </script>
@stop