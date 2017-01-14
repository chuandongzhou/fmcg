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
                              data-done-url="{{ url('auth/register-set-password') }}"
                              accept-charset="UTF-8"
                              autocomplete="off">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
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
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">用户账号:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control" placeholder="请输入用户账号" type="text" name="user_name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">保密手机:</span>
                                    </label>
                                    <div class="col-xs-4">
                                        <input class="form-control" placeholder="请输入手机号码" type="text"
                                               name="backup_mobile">
                                    </div>
                                    <div class="col-xs-2 v-code">
                                        <button type="button"
                                                class="btn btn-warning  form-control send-sms no-prompt geetest-btn"
                                                data-url="{{ url('api/v1/auth/reg-send-sms') }}" data-method="post"
                                                data-done-then="none" data-prevent-default="none">获取验证码
                                        </button>
                                        <div id="mask"></div>
                                        <div id="popup-captcha">
                                            {!! Geetest::render('popup') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">验证码:</span>
                                    </label>
                                    <div class="col-xs-8 col-md-6">
                                        <input class="form-control" placeholder="请输入手机验证码" type="text" name="code">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-8 col-xs-offset-3">
                                        <button type="submit"
                                                class="btn btn-warning btn-submit">下一步
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
            //验证码的显示与隐藏
            $("#mask").click(function () {
                $("#mask, #popup-captcha").hide();
            });
            $(".send-sms").click(function () {
                $("#mask, #popup-captcha").show();
                $(this).attr('type', 'submit');
            });
            //短信发送成功倒计时
            $('.send-sms').on('done.hct.ajax', function (data, textStatus) {
                var $this = $(this);
                $this.next('button').prop('disabled', true).removeClass('btn-success').addClass('btn-default');
                // 成功进行倒计时
                timeIntervalFunc({
                    tick: function (i) {
                        $this.next('button').html(i + ' 秒后重试');
                    },
                    done: function () {
                        $this.next('button').prop('disabled', false).removeClass('btn-default').addClass('btn-success');
                        $this.next('button').html('重新获取');
                    },
                    count: 60
                })
            }).on('fail.hct.ajax', function (jqXHR, textStatus, errorThrown) {
                var $this = $(this);
                var json = textStatus['responseJSON'];
                $this.next('button').html(typeof(json)!='undefined'?json['message']: '获取失败').prop('disabled', true);
                setTimeout(function () {
                    $this.next('button').html('重新获取').prop('disabled', false);
                }, 2000);


            });

        });
    </script>
@stop