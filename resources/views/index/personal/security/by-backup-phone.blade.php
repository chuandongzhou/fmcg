@extends('index.menu-master')

@section('subtitle', '个人中心-安全设置')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> >
    <a href="{{ url('personal/security/index') }}">安全设置</a>>
    <span class="second-level">修改密码</span>
@stop
@section('right')
    <div class="row margin-clear">
        <div class="col-sm-12 security-setting-wrap">
            <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/validate-backup-sms') }}"
                  method="post"
                  data-done-url="{{ $type=='password'?url('personal/security/edit-password'):url('personal/security/edit-backup-phone') }}">
                <div class="item title">
                    {{ $type=='password'?'您在通过原来密保手机验证码重新设置登录密码':'您在通过原来密保手机验证码重新设置密保手机号' }}
                </div>
                <div class="item">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> 密保手机 : </label>
                        <div class="col-sm-5 clearfix get-code">
                            {{ substr_replace($backupPhone,'****',3,4) }}
                            <button class="btn code-btn send-sms" data-url="{{url('api/v1/personal/backup-sms')}}">
                                获取验证码
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> 手机验证码 : </label>
                        <div class="col-sm-5">
                            <input class="form-control" name="code" id="code"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> </label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-success">下一步</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script>
        $(function () {
            var timer;
            //获取验证码
            $('.send-sms').click(function () {
                var obj = $(this), url = obj.data('url');
                obj.prop('disabled', true);
                obj.removeClass('btn-success').addClass('btn-default');
                var i = 60;
                timer = setInterval(function () {
                    // 继续
                    if (--i > 0) {
                        obj.html(i + ' 秒后重试');
                        return;
                    }
                    if (i == 0) {
                        obj.prop('disabled', false);
                        obj.html('重新获取');
                        clearInterval(timer);
                    }
                }, 1000);
                $.ajax({
                    url: url,
                    method: 'get'
                }).fail(function (jqXHR) {
                    clearInterval(timer);
                    obj.button('fail');
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {

                            obj.html('发送失败');
                            setTimeout(function () {
                                obj.html('重新获取');
                                obj.prop('disabled', false);
                            }, 1000);
                        }, 0);
                    }
                    obj.html('重新获取');
                });

            });
            //提交表单验证码按钮操作
            $('.next-btn').click(function () {
                $('.send-sms').html('获取验证码').prop('disabled', false);
                clearTimeout(timer);
            });
        });
    </script>
@stop

