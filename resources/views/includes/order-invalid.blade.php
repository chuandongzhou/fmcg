<!-- 弹出层 -->
@section('body')
    <div class="modal fade in" id="invalid" tabindex="-1" role="dialog" aria-labelledby="invalidModalLabel"
         aria-hidden="true" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width:70%;margin:auto">
                <form class="form-horizontal ajax-form" method="post" data-help-class="col-sm-push-2 col-sm-10"
                      autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="refundModalLabel">
                            订单作废 <span class="extra-text"></span>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="modal-title form-group" id="cropperModalLabel">
                            <label class="col-sm-2 text-right control-label">作废原因:</label>

                            <div class="extra-text col-sm-8">
                                <textarea class="form-control" rows="4" name="reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel btn-sm btn-close" data-dismiss="modal">取消作废</button>
                        <button type="submit" class="btn btn-red pay" data-text="确定作废">确定作废</button>
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
            var payModal = $('#invalid');
            payModal.on('show.bs.modal', function (e) {
                var payParent = $(e.relatedTarget),
                        url = payParent.data('url');
                payModal.find('button[type="submit"]').data('url', url);
            });
        })
    </script>
@stop