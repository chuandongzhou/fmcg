<div class="modal fade in" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                @if(isset($delivery_man) && $delivery_man->count())
                    <p class="modal-title" id="cropperModalLabel">选择配送人员:
                            <span class="extra-text">
                                  <select name="delivery_man_id">
                                      @foreach($delivery_man as $index => $item)
                                          <option value="{{ $index }}">{{ $item }}</option>
                                      @endforeach
                                  </select>
                            </span>
                    </p>
                @else
                    没有配送人员信息,请设置。<a href="{{ url('personal/delivery-man') }}">去设置</a>
                @endif
            </div>
            <div class="modal-body">
                <div class="text-right">
                    <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                    </button>
                    @if(isset($delivery_man) && $delivery_man->count())
                        <input type="hidden" name="order_id" value=""/>
                        <button type="button" class="btn btn-primary btn-sm btn-add ajax btn-send"
                                data-text="确定" data-url="{{ url('api/v1/order/batch-send') }}"
                                data-method="put">确定
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>