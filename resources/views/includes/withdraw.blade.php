<div class="modal fade in" id="withdraw" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/withdraw/add-withdraw') }}"
                  method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <p class="modal-title">提现金额:
                            <span class="extra-text">
                              ￥<input type="text" name="amount"/>
                            </span>
                        <span class="tip" style="display: none;color:red;">请输入数字</span>
                    </p>
                </div>
                <div class="modal-header">
                    <p class="modal-title">提现账号:
                            <span class="extra-text">
                                @if(empty($bankInfo))
                                    没有账号
                                @else
                                    <select name="bank_id">
                                        @foreach($bankInfo as $bank)
                                            <option {{$bank->is_default == 1 ? 'selected' :""}} value="{{ $bank->id }}">{{ $bank->card_holder }}
                                                --{{ $bank->card_number }}--{{ $bank->card_address }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </span>
                    </p>
                </div>
                <div class="modal-body">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="确定">确定
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
