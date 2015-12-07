<div class="modal fade in" id="changePrice" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
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
                    <button type="button" class="btn btn-primary btn-sm btn-add ajax" data-text="确定"
                            data-url="{{ url('api/v1/order/change-price') }}" data-method="put">确定
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>