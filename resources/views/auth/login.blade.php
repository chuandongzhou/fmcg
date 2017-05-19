@extends('auth.master')

@section('title' , '登录|订百达 - 订货首选')

@section('body')
    <div class="container-fluid login-container">
        <div class="row">
            <div class="col-xs-12 text-center login-title">
                <h3>登录系统</h3>
            </div>
        </div>
        <div class="row content-panel">
            <div class="col-xs-8 col-xs-offset-2 login-content">
                <div class="col-xs-12 logo text-right">
                    <img src="{{ asset('images/logo.png') }}">
                </div>
                <div class="col-xs-7 login-left-img">
                    <img src="{{ asset('images/login-banner.jpg') }}">
                </div>
                <div class="col-xs-5 login-right-content">
                    <div class="row login-wrap">
                        <form class="ajax-form" method="post" action="{{ url('api/v1/auth/login') }}"
                              accept-charset="UTF-8" data-help-class="error-msg text-center"
                        >
                            <div class="col-xs-12 padding-clear">
                                <span class="role-title">终端平台</span>
                                <input type="hidden" name="type" id="type" value="{{ cons('user.type.retailer')  }}"/>
                            </div>
                            <div class="col-xs-12 padding-clear item text-center">
                                <div class="enter-item form-group">
                                    <span class="icon icon-name"></span>
                                    <span class="line"></span>
                                    <input type="text" name="account" class="user-name" placeholder="用户名">
                                </div>
                                <div class="enter-item form-group">
                                    <span class="icon icon-password"></span>
                                    <span class="line"></span>
                                    <input type="password" class="password" placeholder="密码" name="password">
                                </div>
                                <span class="triangle-left"></span>
                                <span class="triangle-right"></span>
                            </div>

                            <div class="col-xs-12 btn-item text-center">
                                <button type="button" class="register-btn btn btn-primary" data-toggle="modal"
                                        data-target="#myModal-agreement">注册
                                </button>
                                <button type=" {{ request()->cookie('login_error') >=2?'button':'submit' }} "
                                        class="login-btn btn btn-warning no-prompt geetest-btn">登录
                                </button>
                                <div id="mask"></div>
                                <div id="popup-captcha">
                                    @if(request()->cookie('login_error') >=2)
                                        {!! Geetest::render('popup') !!}
                                    @endif
                                </div>
                            </div>

                            <div class="col-xs-12 text-right forget-pwd">
                                <div class="col-sm-6 text-left">
                                    <a href="{{ url('child-user/auth/login') }}" target="_blank">子帐号登录</a>
                                </div>
                                <div class="text-right col-sm-6">
                                    <a href="javascript:" data-toggle="modal" data-target="#backupModal">忘记密码 ?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-xs-offset-3 change-role-options">
                <a class="tabs-item">
                    <div class="item-icon">
                        <img src="{{ asset('images/guide-icons-1.png')  }}">
                    </div>
                    <span class="item-name" data-type="{{ cons('user.type.supplier') }}">供应平台</span>
                </a>
                <a class="tabs-item">
                    <div class="item-icon">
                        <img src="{{ asset('images/guide-icons-2.png') }}">
                    </div>
                    <span class="item-name" data-type="{{ cons('user.type.wholesaler') }}">批发平台</span>
                </a>
                <a class="tabs-item">
                    <div class="item-icon">
                        <img src="{{ asset('images/guide-icons-3.png') }}">
                    </div>
                    <span class="item-name " data-type="{{ cons('user.type.retailer') }}">终端平台</span>
                </a>
                <a class="tabs-item">
                    <div class="item-icon">
                        <img src="{{ asset('images/guide-icons-4.png') }}">
                    </div>
                    <span class="item-name" data-type="{{ cons('user.type.retailer') }}">零售商城</span>
                </a>
            </div>
        </div>
    </div>
    @include('includes.backup-password')
    @include('includes.agreement')
@stop


@section('js')
    @parent
    <script src="https://static.geetest.com/static/tools/gt.js"></script>
    <script type="text/javascript">
        $(function () {
            $("body").addClass("login-body");
            $(".tabs-item").click(function () {
                var self = $(this), roleName = self.children(".item-name").text();
                $(".role-title").text(roleName);
                $('#type').val(self.children(".item-name").data('type'));
            });
            //验证码的显示与隐藏
            $("#mask").click(function () {
                $("#mask, #popup-captcha").hide();
            });
            $(".login-btn").click(function () {
                $(this).attr('type', 'submit');
                $("#mask, #popup-captcha").show();
            });
            //登录失败事件
            $('.ajax-form').on('fail.hct.ajax', function (jqXHR, textStatus, errorThrown) {
                $(".login-btn").html('登录').button('reset');
                var json = textStatus['responseJSON'];
                if (json) {
                    if (json['id'] == 'invalid_params') {
                        if (json['errors'].loginError) {
                            if (json['errors'].loginError >= 2) {
                                //连续登录失败两次以上
                                if (!$('#popup-captcha div').length) {
                                    //页面未加载验证码
                                    var handlerEmbed = function (captchaObj) {
                                        $("#popup-captcha").closest('form').submit(function (e) {
                                            var validate = captchaObj.getValidate();
                                            if (!validate) {
                                                alert('请完成验证');
                                                e.preventDefault();
                                            }
                                        });
                                        captchaObj.bindOn($('#popup-captcha').closest('form').find('.login-btn'));
                                        captchaObj.appendTo("#popup-captcha");

                                    };
                                    $.ajax({
                                        url: '/auth/geetest' + "?t=" + (new Date()).getTime(),
                                        type: "get",
                                        dataType: "json",
                                        success: function (data) {
                                            initGeetest({
                                                gt: data.gt,
                                                challenge: data.challenge,
                                                product: "popup",
                                                offline: !data.success,
                                                lang: 'zh-cn'
                                            }, handlerEmbed);
                                        }
                                    });
                                }
                                $('.login-btn').attr('type', 'button');
                            }
                            delete json['errors']['loginError'];
                        }
                        $('form').formValidate(json['errors']);
                    }
                }
                return false;

            });
        });
    </script>
@stop