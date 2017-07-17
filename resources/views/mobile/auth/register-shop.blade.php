@extends('mobile.master')

@section('subtitle', '填写商户信息')

@include('includes.uploader')

@section('body')
    @parent
    <div class="fixed-header fixed-item">
        注册
    </div>
    <form class="mobile-ajax-form" action="{{  url('api/v1/auth/register') }}" method="post"
          data-done-url="{{ url('auth/register-success') }}" enctype="multipart/form-data">
        <div class="container reg-container">
            <div class="row  margin-clear">
                <ul class="reg-step">
                    <li class="step-item on">创建账户 ></li>
                    <li class="step-item on">设置密码 ></li>
                    <li class="step-item on">填写商户信息 ></li>
                    <li class="step-item">注册成功</li>
                </ul>
            </div>
            <div class="row edit-wrap">
                <div class="col-xs-12 enter-panel">
                    <div class="item bordered">
                        <span class="control-label">店家名称</span>
                        <input type="text" class="pull-right" name="name" placeholder="20个汉字以内"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">联系人</span>
                        <input type="text" class="pull-right" name="contact_person" placeholder="10个汉字以内"/>
                    </div>
                    <div class="item bordered">
                        <span class="control-label">联系方式</span>
                        <input type="text" class="pull-right" name="contact_info" placeholder="11位数字"/>
                    </div>
                    <div class="item">
                        <div class="control-label">营业执照注册号</div>
                        <div class="whole-line-item">
                            <input type="text" name="license_num" placeholder="20位数字以内"/>
                        </div>
                    </div>
                    <div class="item">
                        <span class="control-label">食品流通许可证</span>

                        <div class="update-img pull-right image-upload">
                            <a href="javascript:;">
                                <input type="file" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                                       accept="images/*" data-name="business_license">
                            </a>
                            <i class="iconfont icon-jiantouyoujiantou right-arrow"></i>
                        </div>
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
                </div>
                <div class="col-xs-12 not-required-title">
                    非必填项
                </div>
                <div class="col-xs-12 enter-panel last-panel">
                    <div class="item">
                        <span class="control-label">营业执照</span>
                        <div class="update-img pull-right image-upload">
                            <a href="javascript:;">
                                <input type="file" data-url="{{ url('api/v1/file/upload-temp') }}" name="file"
                                       accept="images/*" data-name="license">
                            </a>
                            <i class="iconfont icon-jiantouyoujiantou right-arrow"></i>
                        </div>
                    </div>
                    <div class="item">
                        <span class="control-label">推广码</span>
                        <input type="text" name="spreading_code" class="promotion-code pull-right"/>
                    </div>
                </div>
                <div class="hidden">
                    <input type="hidden" name="user_name" value="{{ $user['user_name'] }}"/>
                    <input type="hidden" name="type" value="{{ $user['type'] }}"/>
                    <input type="hidden" name="backup_mobile" value="{{ $user['backup_mobile'] }}"/>
                    <input type="hidden" name="password" value="{{ $user['password'] }}"/>
                    <input type="hidden" name="password_confirmation" value="{{ $user['password_confirmation'] }}"/>
                    <!-- address data-->
                    <input type="hidden" name="address[province_id]">
                    <input type="hidden" name="address[city_id]">
                    <input type="hidden" name="address[district_id]">
                    <input type="hidden" name="address[street_id]">
                    <input type="hidden" name="address[area_name]">
                    <!-- address data-->
                </div>
            </div>
        </div>
        <div class="fixed-footer fixed-item">
            <button type="submit"> 下一步</button>
        </div>
    </form>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('mobile/dialog.js') }}"></script>
    <script type="text/javascript" src="{{ asset('mobile/mobile-select-area.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var json = formatAddress(addressData)
                , addressArea = $('#address-area')
                , addressStreet = $('#address-street')
                , addressName = ''
                , provinceInput = $('input[name="address[province_id]"]')
                , cityInput = $('input[name="address[city_id]"]')
                , districtInput = $('input[name="address[district_id]"]')
                , streetInput = $('input[name="address[street_id]"]')
                , areaNameInput = $('input[name="address[area_name]"]');
            addressSelect(json, '#txt_area', addressArea, function (scroller, text, value) {
                addressStreet.html('');
                $('#txt_street').unbind('click');
                addressName = text.join('');
                addressArea.html(addressName);
                provinceInput.val(value[0]);
                cityInput.val(value[1]);
                districtInput.val(value[2]);
                streetInput.val(0);
                areaNameInput.val(addressName);
                if (value[2]) {
                    setStreetArea(value[2], addressStreet, streetInput, areaNameInput);
                }
            });
            imageUpload();

            /* $('input[type="file"]').change(function () {
             var objUrl = getObjectURL(this.files[0]);
             $(this).prev('img').removeClass('hidden').attr('src', objUrl);
             })*/
        });


    </script>
@stop