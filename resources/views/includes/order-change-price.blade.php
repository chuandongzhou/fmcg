<div class="modal fade in" id="changePrice" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/order/change-price') }}"  method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
               <input type="hidden" name="_method" value="put">
                <input type="hidden" name="order_id" />
                <input type="hidden" name="pivot_id" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <p class="modal-title" id="cropperModalLabel">修改单价:
                            <span class="extra-text">
                                  <input type="text" name="price"/>
                                <span class="tip" style="display: none;color:red;">请输入数字</span>
                            </span>
                    </p>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="确定">
                            确定
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>