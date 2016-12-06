@extends('auth.master')

@section('title' , '注册成功 | 订百达')

@section('body')
    @parent
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-xs-12 register-step-wrap text-center">
                <div class="item item-text step-active">
                    创建账户
                </div>
                <div class="item step-active">--------------</div>
                <div class="item item-text step-active">
                    设置密码
                </div>
                <div class="item step-active">--------------</div>
                <div class="item item-text step-active">
                    填写商户信息
                </div>
                <div class="item step-active">--------------</div>
                <div class="item item-text step-active">
                    注册成功
                </div>
            </div>
            <div class="col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-2">
                <div class="panel panel-auth register">
                    <div class="panel-body">
                        <div class="title">
                            <div>
                                恭喜你注册成功,您的账号<span class="title-username">{{ $user_name }}</span>,为了防止账号被盗请妥善保管好账号密码。
                            </div>
                            <div class="go-login">
                                <span class="go-time" id="jumTo">3</span>秒后跳转至首页,<a
                                        href="{{ url('auth/login') }}">去登陆</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var t;
            function delayURL() {
                var delay = $(".go-time").html();
                if (delay > 0) {
                    delay--;
                    $(".go-time").html(delay);
                    setTimeout("delayURL()", 1000)
                } else {
                    clearInterval(t);
                    window.location.href = site.baseUrl; //跳转首页地址
                }
            }
            window.onload = function () {
                t = setInterval(function () {
                    delayURL();
                }, 3000)
            }
        })
    </script>
@stop
