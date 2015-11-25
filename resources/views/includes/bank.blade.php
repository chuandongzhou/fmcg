@section('body')
    <div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">添加银行账号<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body address-select">
                    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/bank') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_number">卡号:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="card_number" name="card_number" placeholder="请输入银行卡号"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact_info">所属银行:</label>

                            <div class="col-sm-4 col-md-4">
                                <select name="card_type" class="form-control">
                                    @foreach(cons()->valueLang('bank.type') as $key => $type)
                                        <option value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_holder">开户人:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="card_holder" name="card_holder" placeholder="请输入开户人"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="card_address">开户行所在地:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="card_address" name="card_address"
                                       placeholder="请输入开户行所在地"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="添加">添加</button>
                                <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop