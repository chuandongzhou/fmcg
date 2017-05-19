@section('body')
    <div class="modal fade" id="childUserModal" tabindex="-1" role="dialog" aria-labelledby="childUserModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/child-user') }}"
                      method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="deliveryModalLabel">
                            <span>添加子帐号</span>
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
                            <label class="col-sm-2 control-label" for="user_name">登录名:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="account" name="account"
                                       placeholder="请输入登录名"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password">登录密码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password" name="password" placeholder="请输入登录密码"
                                       value=""
                                       type="password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="password_confirmation">密码确认:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="password_confirmation" name="password_confirmation"
                                       placeholder="请再次输入登录密码"
                                       value=""
                                       type="password">
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
            var childUserModal = $('#childUserModal'),
                form = childUserModal.find('form');
            childUserModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#childUserModalLabal span').html(obj.hasClass('add') ? '添加子帐号' : '编辑子帐号');
                $('.btn-add').html('提交');
                var id = obj.data('id') || '',
                    name = obj.data('name') || '',
                    phone = obj.data('phone') || '',
                    account = obj.data('account') || '';

                $('input[name="name"]').val(name);
                $('input[name="phone"]').val(phone);
                account && $('input[name="account"]').val(account).prop('disabled', true);
                form.attr('action', site.api(obj.hasClass('add') ? 'personal/child-user' : 'personal/child-user/' + id));
                form.attr('method', obj.hasClass('add') ? 'post' : 'put');

            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
            });

        });
    </script>
@stop
