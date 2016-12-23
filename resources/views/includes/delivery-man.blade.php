@section('body')
    <div class="modal fade" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-man') }}"
                      method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="deliveryModalLabel">
                            <span>添加配送人员</span>
                        </div>
                    </div>
                    <div class="modal-body address-select">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">姓名:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="name" name="name" placeholder="请输入姓名"
                                       value=""
                                       type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="phone">手机号码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="phone" name="phone" placeholder="请输入手机号码"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="user_name">POS机登录名:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="user_name" name="user_name"
                                       placeholder="请输入POS机登录名（必须为6位数字）"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password">POS机登录密码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password" name="password" placeholder="请输入POS机登录密码"
                                       value=""
                                       type="password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password_confirmation">POS机密码确认:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password_confirmation" name="password_confirmation"
                                       placeholder="请输入POS机登录密码"
                                       value=""
                                       type="password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">POS机编号:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="pos_sign" name="pos_sign" placeholder="请输入POS机编号"
                                       value=""
                                       type="text">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer middle-footer">
                        <button type="submit" class="btn btn-success btn-sm btn-add pull-right" data-text="添加">添加
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var deliveryModal = $('#deliveryModal'),
                    form = deliveryModal.find('form');
            deliveryModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#deliveryModal span').html(obj.hasClass('add') ? '添加配送人员' : '编辑配送人员');
                $('.btn-add').html('提交');
                var id = obj.data('id') || '',
                        name = obj.data('name') || '',
                        phone = obj.data('phone') || '',
                        userName = obj.data('user-name') || '',
                        posSign = obj.data('pos-sign') || '';
                $('input[name="name"]').val(name);
                $('input[name="phone"]').val(phone);
                $('input[name="user_name"]').val(userName);
                $('input[name="pos_sign"]').val(posSign);
                form.attr('action', site.api(obj.hasClass('add') ?'personal/delivery-man':'personal/delivery-man/' + id));
                form.attr('method', obj.hasClass('add') ? 'post' : 'put');

            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
            });

        });
    </script>
@stop
