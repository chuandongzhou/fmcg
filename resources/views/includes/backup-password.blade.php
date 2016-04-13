@section('body')
    <div class="modal modal1 fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-center forgot-modal-title" id="myModalLabel">
                        找回密码
                    </h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-xs-3 control-label" for="user_name">
                                <span class="prompt">用户账号:</span></label>

                            <div class="col-xs-8">
                                <input class="form-control" name="user_name" placeholder="请输入用户账号" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label" for="secret_phone ">
                                <span class="prompt">密保手机:</span></label>

                            <div class="col-xs-8">
                                <input class="form-control" name="secret_phone" placeholder="请输入密保手机" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label" for="license_num">
                                <span class="prompt">营业执照注册号:</span></label>

                            <div class="col-xs-8">
                                <input class="form-control" name="license_num" placeholder="请输入营业执照注册号" type="text">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary">确认</button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js')
    @parent
    <script>
        $(function () {
            var cropper = $('#backupModal'),
                    userName = cropper.find('input[name="user_name"]'),
                    backupMobile = cropper.find('input[name="backup_mobile"]'),
                    licenseNum = cropper.find('input[name="license_num"]'),
                    password = cropper.find('input[name="password"]');
            passwordConfirm = cropper.find('input[name="password_confirmation"]');

            cropper.on('hidden.bs.modal', function () {
                userName.val('');
                backupMobile.val('');
                licenseNum.val('');
                password.val('');
                passwordConfirm.val('');
            });
            $('.send-sms').on('always.hct.ajax', function (data, textStatus) {
                if (textStatus.status >= 200 && textStatus.status < 300) {
                    var $this = $(this);
                    // 成功进行倒计时
                    timeIntervalFunc({
                        tick: function (i) {
                            $this.html(i + ' 秒后重试');
                        },
                        done: function () {
                            $this.button('reset');
                        },
                        count: 120
                    });
                }
                return true;

            })
        });
    </script>
@stop