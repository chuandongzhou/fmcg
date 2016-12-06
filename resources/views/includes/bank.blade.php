@section('body')
    <div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="bankModalLabel">
                        <span>添加银行账号</span>
                    </div>
                </div>
                <div class="modal-body address-select">
                    <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/bank') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
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
                                <select name="card_type" id="cardType" class="form-control">
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
                        <div class="form-group row ">
                            <div class="modal-footer middle-footer">
                                <button type="submit" class="btn btn-success btn-sm btn-add" data-text="添加">添加</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var bankModal = $('#bankModal'),
                    form = bankModal.find('form');
            $('.update-modal').click(function () {
                var obj = $(this);
                if (obj.hasClass('add-bank-account')) {
                    form.attr('action', site.api('personal/bank'));
                    form.attr('method', 'post');

                } else {
                    $('#bankModal span').html('编辑银行账号');
                    var id = obj.data('id'),
                            cardNumber = obj.data('card-number'),
                            cardType = obj.data('card-type'),
                            cardHolder = obj.data('card-holder'),
                            cardAddress = obj.data('card-address');
                    $('input[name="card_number"]').val(cardNumber);
                    $('input[name="card_holder"]').val(cardHolder);
                    $('input[name="card_address"]').val(cardAddress);
                    $('select[name="card_type"] option[value=' + cardType + ']').attr("selected", "selected");
                    form.attr('action', site.api('personal/bank/' + id));
                    form.attr('method', 'put');


                }
            });
            bankModal.on('hidden.bs.modal', function (e) {

                $('input[name="card_number"]').val('');
                $('input[name="card_holder"]').val('');
                $('input[name="card_address"]').val('');
                $("#cardType option:first").attr("selected", "selected");
            });

        });
    </script>
@stop