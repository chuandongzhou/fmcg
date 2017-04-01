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
                          method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true"
                          autocomplete="off">
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

                            <div class="col-sm-10">
                                <select class="address address-province inline-control" name="bank_province">
                                    <option selected="selected" value="">请选择省市/其他...</option>
                                </select>

                                <select class="address address-city inline-control" name="bank_city">
                                    <option selected="selected" value="">请选择城市...</option>
                                </select>

                                <input type="hidden" name="card_address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="bank_name">开户行名称:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="bank_name" name="bank_name" placeholder="请输入开户行名称">
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

@section('js-lib')
    @parent
    <script type="application/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var bankModal = $('#bankModal'), form = bankModal.find('form');
            bankModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#bankModal span').html(obj.hasClass('add-bank-account') ? '添加银行账号' : '编辑银行账号');
                $('.btn-add').html(obj.hasClass('add-bank-account') ? '添加' : '提交');
                var id = obj.data('id') || 0,
                    cardNumber = $('input[name="card_number"]'),
                    cardHolder = $('input[name="card_holder"]'),
                    cardAddress = $('input[name="card_address"]'),
                    cardType = $('select[name="card_type"]'),
                    bankProvince = $('select[name="bank_province"]'),
                    bankCity = $('select[name="bank_city"]'),
                    bankName = $('input[name="bank_name"]')


                $('.address').on('change', function () {
                    var province = $('.address-province').val() ? $('.address-province option:checked').text() : '',
                        city = $('.address-city').val() ? $('.address-city option:checked').text() : '';
                    cardAddress.val(province + city);
                });

                if (id) {
                    $.ajax({
                        url: site.api('personal/bank/' + id),
                        method: 'get'
                    }).done(function (data) {
                        var bank = data.bank;
                        cardNumber.val(bank.card_number);
                        cardHolder.val(bank.card_holder);
                        cardType.val(bank.card_type);
                        bankProvince.data('id', bank.bank_province);
                        bankCity.data('id', bank.bank_city);
                        bankName.val(bank.bank_name);
                        cardAddress.val(bank.card_address);
                        new Address(bankProvince, bankCity);
                    });
                }
                form.attr('action', site.api(id ? 'personal/bank/' + id : 'personal/bank'));
                form.attr('method', id ? 'put' : 'post');
            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
                form.trigger('reset');
            })
        });
    </script>
@stop