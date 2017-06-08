@extends('mobile.master')

@section('subtitle', '创建账户')

@section('body')
    @parent
    <div class="fixed-header fixed-item">
        注册
    </div>
    <form class="mobile-ajax-form" action="{{ url('api/v1/auth/register-user') }}" method="post"
          data-done-url="{{ url('auth/register-password') }}">
        <div class="container reg-container">
            <div class="row  margin-clear">
                <ul class="reg-step">
                    <li class="step-item on">创建账户 ></li>
                    <li class="step-item">设置密码 ></li>
                    <li class="step-item">填写商户信息 ></li>
                    <li class="step-item">注册成功</li>
                </ul>
            </div>
            <div class="row enter-wrap">
                <div class="col-xs-12 ">

                    <div class="enter-item">
                        <div class="icon-panel role-panel">
                            <a href="javascript:;" class="select-role">
                                <img class="active" src="{{ asset('images/mobile-images/role_1.png') }}">
                                <img src="{{ asset('images/mobile-images/role_2.png') }}">
                                <img src="{{ asset('images/mobile-images/role_3.png') }}">
                            </a>
                            <span class="triangle">
                                <input type="hidden" name="type" value="{{ cons('user.type.retailer') }}">
                            </span>
                        </div>
                        <input type="text" name="user_name" placeholder="请输入账户名"/>
                    </div>
                    <div class="enter-item secret-phone">
                        <div class="row">
                            <div class="col-xs-8 enter-item">
                                <input type="text" name="backup_mobile" placeholder="请输入密保手机"/>
                            </div>
                            <div class="col-xs-4 pd-right-clear">
                                <button type="submit"  data-url="{{ url('api/v1/auth/reg-send-sms') }}"
                                        data-method="post" data-done-then="none"
                                        class="get-code margin-clear send-sms" data-prevent-default="none">获取验证码
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="enter-item">
                        <input type="text" name="code" placeholder="请输入短信验证码"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item">
            <button type="submit"> 下一步</button>
        </div>
    </form>
    @include('mobile.includes.role-select')
@stop


@section('js')
    <script type="text/javascript">
        roleSelect()
        //短信发送成功倒计时
        $('.send-sms').on('done.hct.ajax', function (data, textStatus) {
            var $this = $(this);
            $this.prop('disabled', true).removeClass('btn-success').addClass('btn-default');
            // 成功进行倒计时
            timeIntervalFunc({
                tick: function (i) {
                    $this.html(i + ' 秒后重试');
                },
                done: function () {
                    $this.prop('disabled', false);
                    $this.prop('disabled', false).removeClass('btn-default').addClass('btn-success');
                    $this.html('重新获取');
                },
                count: 60
            })
        }).on('fail.hct.ajax', function (jqXHR, textStatus, errorThrown) {
            var $this = $(this);
            var json = textStatus['responseJSON'];
            $this.html(typeof(json) != 'undefined' ? json['message'] : '获取失败').prop('disabled', true);
            setTimeout(function () {
                $this.html('重新获取').prop('disabled', false);
            }, 2000);
        });
    </script>
@stop