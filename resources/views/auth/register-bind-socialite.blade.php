@extends('auth.master')
@include('includes.uploader')
@include('includes.address', ['model' => 'shop'])
@include('includes.backup-password')

@section('title' , '注册 | 订百达')

@section('body')
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-xs-8 col-xs-offset-2 bindwx-step-wrap text-center">
                <div class="col-xs-6 step-item active">已有订百达账号，请绑定</div>
                <div class="col-xs-6 step-item">没有订百达账号，请完善资料</div>
            </div>
            <div class="col-xs-12 register bindwx-register">
                <div class="row ">
                    <div class="col-xs-8 col-xs-offset-2 login-msg-panel text-center">
                        <img class="" src="{{ $token['avatar'] }}" alt="头像"/>
                        <div class=" msg-tips">Hi,{{ $token['nickname'] }} 欢迎来到订百达 , 完成绑定后可以微信账号一键登录哦~</div>
                    </div>
                    <div class="col-xs-8 col-xs-offset-2">
                        <form class="ajax-form form-horizontal" action="{{ url('api/v1/auth/bind-socialite') }}" data-done-url="{{ url('auth/reg-success') }}" method="post" accept-charset="UTF-8">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="name"><span class="red">*</span>
                                        <span class="prompt">店家名称:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                                               value=""
                                               type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="username"><span class="red">*</span>
                                        <span
                                                class="prompt">联系人:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="contact_person" name="contact_person"
                                               placeholder="请输入联系人" type="text">

                                    </div>
                                </div>
                                <div class="form-group visible-item visible-item-2 visible-item-3 visible-item-4">
                                    <label class="col-xs-3 control-label" for="license_num"><span class="red">*</span>
                                        <span class="prompt">营业执照注册号:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="license_num" name="license_num"
                                               placeholder="请输入执照注册号" type="text">
                                    </div>
                                </div>
                                <div class="form-group visible-item visible-item-2 visible-item-3 visible-item-4">
                                    <label class="col-sm-3 control-label" for="license"><span class="red">*</span> 营业执照:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="license" class="btn btn-primary btn-sm fileinput-button"
                                              name="license">
                                            请选择图片文件
                                            <input type="file" accept="image/*"
                                                   data-url="{{ url('api/v1/file/upload-temp') }}"
                                                   name="file">
                                        </span>


                                        <div class="image-preview w160">
                                            <img src="" class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group visible-item visible-item-2 visible-item-3 visible-item-4">
                                    <label class="col-sm-3 control-label" for="business_license"><span
                                                class="red">*</span> 食品流通许可证:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="business_license"
                                              class="btn btn-primary btn-sm fileinput-button" name="business_license">
                                            请选择图片文件
                                            <input type="file" accept="image/*"
                                                   data-url="{{ url('api/v1/file/upload-temp') }}"
                                                   name="file">
                                        </span>


                                        <div class="image-preview w160">
                                            <img src=""
                                                 class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group visible-item visible-item-3">
                                    <label class="col-sm-3 control-label" for="username"><span class="red">*</span>
                                        代理合同:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="agency_contract"
                                              class="btn btn-primary btn-sm fileinput-button" name="agency_contract">
                                            请选择图片文件
                                            <input type="file" accept="image/*"
                                                   data-url="{{ url('api/v1/file/upload-temp') }}"
                                                   name="file" disabled>
                                        </span>

                                        <div class="image-preview w160">
                                            <img src=""
                                                 class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group shop-address">
                                    <label class="col-xs-3 control-label"><span class="red">*</span> <span
                                                class="prompt">所在地:</span></label>

                                    <div class="col-xs-4">
                                        <select data-group="shop" name="address[province_id]"
                                                class="address-province form-control address">
                                        </select>
                                    </div>
                                    <div class="col-xs-4">
                                        <select data-group="shop" name="address[city_id]"
                                                class="address-city form-control address">
                                        </select>
                                    </div>
                                    <div class="col-xs-8 col-xs-push-3 padding-clear">
                                        <div class="col-xs-6">
                                            <select data-group="shop" name="address[district_id]"
                                                    class="address-district form-control address">
                                            </select>
                                        </div>
                                        <div class="col-xs-6  ">
                                            <select data-group="shop" name="address[street_id]"
                                                    class="address-street form-control address"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="col-xs-3 control-label"><span class="red">*</span> <span
                                                class="prompt">详细地址:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input type="hidden" name="address[area_name]"/>
                                        <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                                               class="form-control" value="">
                                        <input type="hidden" name="x_lng" value=""/>
                                        <input type="hidden" name="y_lat" value=""/>

                                    </div>
                                    <div class="col-xs-8 col-xs-push-3">
                                        <div id="address-map" class="address-map"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="type"> <span
                                                class="red">*</span> <span class="prompt">用户类型:</span>:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <select name="type" class="form-control visible-select" id="type">
                                            @foreach(cons('user.type') as $val)
                                                <option value="{{ $val }}">{{ cons()->valueLang('user.type' , $val) }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="backup_mobile"><span class="red">*</span>
                                        <span class="prompt">手机号码:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="backup_mobile" name="backup_mobile"
                                               placeholder="请输入手机号码" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">用户密码:</span>
                                    </label>
                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" type="password" name="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">确认密码:</span>
                                    </label>
                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" type="password" name="password_confirmation">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">
                                        <span class="red ">*</span>
                                        <span class="prompt">验证码:</span>
                                    </label>

                                    <div class="col-xs-3 col-md-4">
                                        <input class="form-control" placeholder="请输入手机验证码" type="text" name="code">
                                    </div>
                                    <div class="col-xs-3 col-md-2 v-code pd-left-clear">
                                        <button type="submit"
                                                class="btn btn-warning  form-control send-sms no-prompt"
                                                data-url="{{ url('api/v1/auth/reg-send-sms') }}" data-method="post"
                                                data-done-then="none" data-prevent-default="none"
                                                data-no-loading="true">获取验证码
                                        </button>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <input type="hidden" name="token[token]" value="{{ $token['token'] }}">
                                    <input type="hidden" name="token[type]" value="{{ $token['type'] }}">
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-6 col-md-6 col-xs-offset-3">
                                        <button type="submit" class="btn  btn-warning  btn-submit bindwx-submit">立即注册
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 register bindwx-register on">
                <div class="row ">
                    <div class="col-xs-8 col-xs-offset-2 login-msg-panel text-center">
                        <img class="" src="{{ $token['avatar'] }}" alt="头像"/>
                        <div class=" msg-tips">Hi,{{ $token['nickname'] }} 欢迎来到订百达 , 完成绑定后可以微信账号一键登录哦~</div>
                    </div>
                    <div class="col-xs-8 col-xs-offset-2">
                        <form class="ajax-form form-horizontal" method="post" action="{{ url('api/v1/auth/login') }}"
                              accept-charset="UTF-8">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="type"> <span
                                                class="red">*</span> <span class="prompt">用户类型:</span>:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <select name="type" class="form-control" id="type">
                                            @foreach(cons('user.type') as $val)
                                                <option value="{{ $val }}">{{ cons()->valueLang('user.type' , $val) }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="account"><span class="red">*</span>
                                        <span class="prompt">用户账号:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="name" name="account" placeholder="请输入用户账号"
                                               value=""
                                               type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label" for="password"><span class="red">*</span>
                                        <span class="prompt">输入密码:</span></label>

                                    <div class="col-xs-6 col-md-6">
                                        <input class="form-control" id="password" name="password" type="password">

                                    </div>
                                </div>
                                <div class="hidden">
                                    <input type="hidden" name="token[token]" value="{{ $token['token'] }}">
                                    <input type="hidden" name="token[type]" value="{{ $token['type'] }}">
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-6 col-md-6 col-xs-offset-3 text-right">
                                        <a href="javascript:" class="forget-pwd" data-toggle="modal"
                                           data-target="#backupModal">忘记密码 ?</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div id="mask"></div>
                                    <div id="popup-captcha">
                                        {!! Geetest::render('popup') !!}
                                    </div>
                                    <div class="col-xs-6 col-md-6 col-xs-offset-3">
                                        <button type="submit"
                                                class="btn  btn-warning  btn-submit bindwx-submit geetest-btn">立即绑定
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
@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $(".step-item").click(function () {
                var self = $(this), isActive = self.hasClass("active");
                if (!isActive) {
                    self.addClass("active").siblings().removeClass("active");
                    $(".bindwx-register.on").removeClass("on").siblings().addClass("on");
                } else {
                }
            })
            visibleSelect();

            getShopAddressMap(0, 0);
            picFunc();
            $('select.address').change(function () {
                var provinceControl = $('select[name="address[province_id]"]'),
                    cityControl = $('select[name="address[city_id]"]'),
                    districtControl = $('select[name="address[district_id]"]'),
                    streetControl = $('select[name="address[street_id]"]'),
                    provinceVal = provinceControl.val() ? provinceControl.find("option:selected").text() : '',
                    cityVal = cityControl.val() ? cityControl.find("option:selected").text() : '',
                    districtVal = districtControl.val() ? districtControl.find("option:selected").text() : '',
                    streetVal = streetControl.val() ? streetControl.find("option:selected").text() : '';
                $('input[name="address[area_name]"]').val(provinceVal + cityVal + districtVal + streetVal);
            })

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
                        $this.prop('disabled', false).attr('type', 'button').next('button').prop('disabled', false).removeClass('btn-default').addClass('btn-success').html('重新获取');
                    },
                    count: 60
                })
            }).on('fail.hct.ajax', function (jqXHR, textStatus, errorThrown) {
                var $this = $(this);
                var json = textStatus['responseJSON'];
                $this.next('button').html(typeof(json) != 'undefined' ? json['message'] : '获取失败').prop('disabled', true);
                setTimeout(function () {
                    $this.prop('disabled', false).attr('type', 'button').next('button').html('重新获取').prop('disabled', false);
                }, 2000);
            });

            //验证码的显示与隐藏
            $("#mask").click(function () {
                $("#mask, #popup-captcha").hide();
            });
            $(".login-btn").click(function () {
                $(this).attr('type', 'submit');
                $("#mask, #popup-captcha").show();
            });
            var loadGeetest = false;
            //登录失败事件
            $('.ajax-form').on('fail.hct.ajax', function (jqXHR, textStatus, errorThrown) {
                $(".login-btn").html('登录').button('reset');
                var json = textStatus['responseJSON'];
                if (json && !loadGeetest) {
                    if (json['id'] == 'invalid_params') {
                        if (json['errors'].loginError >= 2) {
                            geetest('{{ Config::get('geetest.geetest_url', '/auth/geetest') }}')
                            loadGeetest = true;
                        }

                    } else if (json['message'] == '请完成验证') {
                        geetest('{{ Config::get('geetest.geetest_url', '/auth/geetest') }}')
                        loadGeetest = true;
                    }
                }
                if (json['errors'] && json['errors']['loginError'])
                    delete json['errors']['loginError'];
            });
        })
    </script>
@stop