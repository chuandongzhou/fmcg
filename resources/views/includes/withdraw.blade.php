<div class="modal fade in" id="withdraw" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
     aria-hidden="true" style="padding-right: 17px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width:70%;margin:auto">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/finance/add-withdraw') }}"
                  method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="cropperModalLabel">
                        账户提现<span class="extra-text"></span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="name">提现金额:</label>

                        <div class="col-sm-10 col-md-6">
                            <input class="form-control" id="amount" name="amount" placeholder="最低提现1000"
                                   value=""
                                   type="text">
                        </div>
                        <div class="col-sm-4">
                            可提现金额 ： <span class="red">{{ sprintf('%.2f' , $balance - $protectedBalance) }}</span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 control-label" for="name">提现账号:</label>

                        <div class="col-sm-10 col-md-8">
                            @if(empty($bankInfo))
                                没有账号
                            @else
                                <select name="bank_id" class="form-control">
                                    @foreach($bankInfo as $bank)
                                        <option {{$bank->is_default == 1 ? 'selected' :""}} value="{{ $bank->id }}">{{ $bank->card_holder }}
                                            --{{ $bank->card_number }}--{{ $bank->card_address }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                    {{--<div class="form-group row">--}}
                        {{--<label class="col-sm-2 control-label" for="name">验证码:</label>--}}

                        {{--<div class="col-sm-5">--}}
                            {{--<input type="text" placeholder="请输入手机验证码" name="backup_mobile" class="form-control">--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-3">--}}
                            {{--<button data-prevent-default="false" data-done-then="none" data-method="post"--}}
                                    {{--data-url="http://fmcg.com/api/v1/auth/send-sms"--}}
                                    {{--class="btn btn-success ajax form-control send-sms" type="submit">发送短信--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消
                        </button>
                        <button type="submit" class="btn btn-success btn-sm btn-add" data-text="确定">确定
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
