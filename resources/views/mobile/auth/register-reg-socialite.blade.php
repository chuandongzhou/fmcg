@extends('mobile.master')
@include('includes.uploader')
@section('subtitle', '注册')

@section('body')
    <div class="fixed-header fixed-item ">
        <div class="row nav-top white-bg orders-details-header">
            <div class="col-xs-2 edit-btn pd-clear">
                <a href="javascript:" onclick="window.history.back()" class="iconfont icon-fanhui2 go-back"></a>
            </div>
            <div class="col-xs-10 color-black">注册</div>
        </div>
    </div>
    <form class="mobile-ajax-form form-horizontal" action="{{ url('api/v1/auth/bind-socialite') }}"
          data-done-url="{{ url('auth/reg-success') }}" method="post" accept-charset="UTF-8">
        <div class="container reg-container">
            <div class="row edit-wrap">
                <div class="col-xs-12 enter-panel">
                    <div class="item bordered">
                        <span class="control-label">联系方式</span>
                        <div class="pull-right right-control">
                            <input type="text" name="backup_mobile" placeholder="请输入密保手机"/>
                            <div class="icon-panel role-panel pull-right">
                                <a href="javascript:;" class="select-role">
                                    <img class="active" src="{{ asset('images/mobile-images/role_1.png') }}">
                                    <img src="{{ asset('images/mobile-images/role_2.png') }}">
                                </a>
                                <span class="triangle">
                                 <input type="hidden" name="type" class="visible-select"
                                        value="{{ cons('user.type.retailer') }}">
                                <input type="hidden" name="token[token]" value="{{ array_get($token, 'token') }}">
                                <input type="hidden" name="token[type]" value="{{ array_get($token, 'type') }}">
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">登录密码</span>
                        <input type="password" name="password" placeholder="请输入密码"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">确认密码</span>
                        <input type="password" name="password_confirmation" placeholder="确认密码"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">验证码</span>
                        <div class="pull-right right-control">
                            <input type="text" name="code" class="bind-code" placeholder="请输入短信验证码"/>
                            <button type="submit" data-url="{{ url('api/v1/auth/reg-send-sms') }}" data-method="post"
                                    data-done-then="none" data-prevent-default="none"
                                    class="bind-get-code margin-clear pull-right send-sms">获取验证码
                            </button>
                        </div>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">店家名称</span>
                        <input type="text" class="pull-right" name="name" placeholder="20个汉字以内"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">联系人</span>
                        <input type="text" class="pull-right" name="contact_person" placeholder="10个汉字以内"/>
                    </div>
                    <div class="item">
                        <span class="control-label">所在地</span>
                        <div class="address pull-right">
                            <a id="txt_area">
                                <span id="address-area"></span>
                                <i class="iconfont icon-jiantouyoujiantou right-arrow pull-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <span class="control-label">街道</span>
                        <div class="address pull-right">
                            <a id="txt_street">
                                <span id="address-street" data-level="1"></span>
                                <i class="iconfont icon-jiantouyoujiantou right-arrow pull-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <input type="hidden" name="address[area_name]"/>
                        <div class="control-label">详细地址</div>
                        <div class="whole-line-item">
                            <input type="text" name="address[address]" placeholder="30个汉字以内"/>
                        </div>
                    </div>

                    <div class="item visible-item visible-item-2">
                        <div class="control-label">营业执照注册号</div>
                        <div class="whole-line-item">
                            <input type="text" name="license_num" placeholder="20位数字以内"/>
                        </div>
                    </div>
                    <div class="item visible-item visible-item-2">
                        <span class="control-label">营业执照</span>
                        <div class="update-img pull-right image-upload">
                            <a href="javascript:;">
                                <input type="file" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                                       accept="images/*" data-name="license">
                            </a>
                            <i class="iconfont icon-jiantouyoujiantou right-arrow"></i>
                        </div>
                    </div>
                    <div class="item visible-item visible-item-2">
                        <span class="control-label">食品流通许可证</span>

                        <div class="update-img pull-right image-upload">
                            <a href="javascript:;">
                                <input type="file" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                                       accept="images/*" data-name="business_license">
                            </a>
                            <i class="iconfont icon-jiantouyoujiantou right-arrow"></i>
                        </div>
                    </div>
                    <div class="item visible-item visible-item-3">
                        <span class="control-label">代理合同</span>

                        <div class="update-img pull-right image-upload">
                            <a href="javascript:;">
                                <input type="file" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                                       accept="images/*" data-name="agency_contract">
                            </a>
                            <i class="iconfont icon-jiantouyoujiantou right-arrow"></i>
                        </div>
                    </div>

                    <div class="hidden">
                        <input type="hidden" name="address[province_id]">
                        <input type="hidden" name="address[city_id]">
                        <input type="hidden" name="address[district_id]">
                        <input type="hidden" name="address[street_id]">
                        <input type="hidden" name="address[area_name]">
                    </div>
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item reg-fixed-item">
            <button type="submit" class="prev-next">立即注册</button>
        </div>
    </form>

    @include('mobile.includes.role-select')
    @parent
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('mobile/dialog.js') }}"></script>
    <script type="text/javascript" src="{{ asset('mobile/mobile-select-area.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            roleSelect();

            $("body").on("click", ".select-role-wrap li a", function () {
                var obj = $(this), type = obj.data('type'), visibleItemSelector = '.visible-item-' + type;
                $('.visible-item').not(visibleItemSelector).each(function () {
                    $(this).addClass('hidden').find('input , select').prop('disabled', true);
                });
                $(visibleItemSelector).each(function () {
                    $(this).removeClass('hidden').find('input , select').prop('disabled', false);
                })
            });

            $('.select-role-wrap li a').trigger('click');

            /**
             * 地址选择
             * @type {Array}
             */
            addressChanged(addressData);

            imageUpload();

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
        })
    </script>
@stop