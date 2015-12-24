<div class="modal fade in" id="refund" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <form class="form-horizontal ajax-form" method="post" data-help-class="col-sm-push-2 col-sm-10"
                  autocomplete="off">
                <input type="hidden" name="_method" value="put">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>

                    <div class="modal-title form-group" id="cropperModalLabel">
                        <label class="col-sm-2 text-right control-label">退款原因:</label>
                            <div class="extra-text col-sm-8">
                                  <textarea class="form-control" rows="4" name="reason"></textarea>
                            </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm" data-text="确定">确定</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>