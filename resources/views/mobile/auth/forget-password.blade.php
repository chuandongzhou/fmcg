@extends('mobile.master')

@section('body')
    @parent
    <div class="fixed-header fixed-item reg-fixed-item">
        忘记密码
    </div>
    <form class="mobile-ajax-form" action="{{ url('api/v1/auth/backup') }}" method="post" data-done-then="none"
          data-no-prompt="true">
        <div class="container reg-container">
            <div class="row edit-wrap forget-wrap">
                <div class="col-xs-12 enter-panel">
                    <div class="item bordered">
                        <span class="control-label">用户账号</span>
                        <input type="text" class="pull-right" name="user_name" placeholder="请输入要找回的账号"/>
                    </div>
                    <div class="item  secret-phone forget-item">
                        <div class="row margin-clear">
                            <div class="col-xs-8 enter-item">
                                <input type="text" name="backup_mobile" placeholder="请输入密保手机"/>
                            </div>
                            <div class="col-xs-4 pd-right-clear">
                                <button type="submit" data-url="{{ url('api/v1/auth/send-sms') }}"
                                        data-method="post" data-done-then="none"
                                        class="get-code margin-clear send-sms" data-prevent-default="none">获取验证码
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="item input-item">
                        <input type="text" class="text-left" maxlength="4" name="code" placeholder="请输入短信验证码"/>
                    </div>
                    <div class="item input-item">
                        <input type="password" class="text-left" name="password" placeholder="请输入密码"/>
                    </div>
                    <div class="item input-item">
                        <input type="password" class="text-left" name="password_confirmation" placeholder="确认密码"/>
                    </div>
                </div>
            </div>

        </div>
        <div class="fixed-footer fixed-item  reg-fixed-item">
            <button type="submit" class="prev-next">完成</button>
        </div>
    </form>
    <!--找回密码 弹出层-->
    <div class="popover-wrap popover-forget-pwd">
        <div class="popover-panel">
            <div class="title text-center">提示</div>
            <div class="content">
                账号<b class="account"></b>密码找回成功
            </div>
            <div class="footer">
                <a href="{{ url('auth/login') }}">去登陆</a>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
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
        $('.mobile-ajax-form').on('done.hct.ajax', function (data, textStatus) {
            if (textStatus.message == '发送成功') {
                return true;
            }
            var container = $('.popover-forget-pwd');
            container.find('.account').html($('.user_name').val());
            layer.open({
                title: false,
                content: container.html(),
                style: ' width:95%; height: auto;  padding:0;',
                shade: 'background-color: rgba(0,0,0,.3)'
            });
            $(".popover-panel").parent().addClass("pd-clear");
        })
    </script>
@stop