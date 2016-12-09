<div class="modal fade in" id="changePrice" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/order/change-order') }}" method="post"
                  data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                <input type="hidden" name="_method" value="put">
                <input type="hidden" name="order_id"/>
                <input type="hidden" name="pivot_id"/>
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="cropperModalLabel">
                        <span>订单修改</span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="price">单价:</label>

                        <div class="col-sm-10 col-md-5">
                            <input type="text" name="price" class="form-control" placeholder="请输入单价"/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="num">数量:</label>

                        <div class="col-sm-10 col-md-5">
                            <input type="text" name="num" class="form-control" placeholder="请输入数量"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success btn-sm btn-add" data-text="确定">
                        确定
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>