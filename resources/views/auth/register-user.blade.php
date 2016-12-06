@extends('auth.master')
@section('title' , '注册 | 订百达')

@section('body')
    @parent
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-xs-12 register-step-wrap text-center">
                <div class="item item-text step-active">
                    创建账户
                </div>
                <div class="item">--------------</div>
                <div class="item item-text">
                    设置密码
                </div>
                <div class="item">--------------</div>
                <div class="item item-text">
                    填写商户信息
                </div>
                <div class="item">--------------</div>
                <div class="item item-text">
                    注册成功
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row register">
                    <div class="col-xs-8">
                        <form class="ajax-form form-horizontal" method="post"
                              action="{{  url('api/v1/auth/register-user')  }}"
                              accept-charset="UTF-8" data-done-url="{{ url('auth/register-set-password') }}" autocomplete="off">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" >
                                        <span class="red ">*</span>
                                        <span class="prompt">用户类型:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <select class="form-control" name="type">
                                            <option value="1">终端商</option>
                                            <option value="2">批发商</option>
                                            <option value="3">供应商</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" >
                                        <span class="red ">*</span>
                                        <span class="prompt">用户账号:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control"  placeholder="请输入用户账号" type="text"  name="user_name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" >
                                        <span class="red ">*</span>
                                        <span class="prompt">保密手机:</span>
                                    </label>
                                    <div class="col-xs-4">
                                        <input class="form-control"  placeholder="请输入手机号码" type="text" name="backup_mobile">
                                    </div>
                                    <div class="col-xs-2 v-code">
                                        <button type="button" class="btn btn-warning  form-control send-sms no-prompt"
                                                data-url="{{ url('api/v1/auth/reg-send-sms') }}" data-method="post"
                                                data-done-then="none" data-prevent-default="false">获取验证码
                                        </button>
                                        <!--<button type="button" class="btn btn-default" disabled>59秒后重新获取验证码</button>-->
                                        <!--<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">重新获取</button>-->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" >
                                        <span class="red ">*</span>
                                        <span class="prompt">验证码:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control"  placeholder="请输入手机验证码" type="text" name="code">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-8 col-xs-offset-3">
                                        <button type="submit" class="btn btn-warning btn-submit">下一步
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('js')
    @parent
    <script>
        $(function () {
            $('.send-sms').click(function(){
                var obj = $(this),
                     url=obj.data('url'),
                     user_name=$('form').find('input[name="user_name"]').val(),
                     backup_mobile=$('form').find('input[name="backup_mobile"]').val();
                if(user_name=="" || backup_mobile==""){
                    alert('用户名和密保手机不能为空');
                    return false;
                }
                obj.prop('disabled', true);
                obj.removeClass('btn-success').addClass('btn-default');
                var i=60;
                var timer = setInterval(function () {
                    // 继续
                    if (--i > 0) {
                        obj.html(i + ' 秒后重试');
                        return;
                    }
                    if(i==0){
                        obj.prop('disabled', false);
                        obj.html('重新获取');
                        clearInterval(timer);
                    }
                }, 1000);
                $.ajax({
                    url: url,
                    method: 'post',
                    data: {user_name: user_name,backup_mobile:backup_mobile}
                }).fail(function (jqXHR) {
                    clearInterval(timer);
                    obj.button('fail');
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {

                            obj.html('发送失败');
                            setTimeout(function(){
                                obj.html('重新获取');
                                obj.prop('disabled', false);
                            },1000);
                        }, 0);
                    }
                    obj.html('重新获取');
                });

            });
        });
    </script>
@stop