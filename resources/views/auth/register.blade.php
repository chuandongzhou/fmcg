@extends('master')
@include('includes.cropper')
@include('includes.address')

@section('title' , '注册 | 订百达')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <nav class="navbar login-nav">
        <div class="container padding-clear register">
            <ul class="nav-title text-center">
                <li><a href="{{ url('auth/guide') }}">首页</a></li>
                <li><a class="logo-icon" href="#"><img src="{{ asset('images/logo.png') }}" alt="logo"/></a></li>
                <li><a href="{{ url('about') }}">关于我们</a></li>
            </ul>
        </div>
    </nav>
    <hr class="register-hr">
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-2">
                <div class="panel panel-auth register">
                    <div class="panel-body">
                        <form class="ajax-form form-horizontal" method="post"
                              action="{{  url('api/v1/auth/register')  }}"
                              accept-charset="UTF-8" data-done-url="{{ url('auth/reg-success') }}" autocomplete="off">
                            <fieldset>
                                <div class="form-group">

                                    <label class="col-sm-3 control-label" for="user_name"><span class="red ">*</span>
                                        用户账号:</label>

                                    <div class="col-sm-8 col-md-6">
                                        <input class="form-control" id="user_name" name="user_name"
                                               placeholder="请输入用户账号"
                                               type="text">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="password"><span class="red">*</span>
                                        用户密码:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="password" name="password" placeholder="请输入密码"
                                               type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="password_confirmation"><span class="red">*</span>
                                        确认密码:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="password_confirmation"
                                               name="password_confirmation" placeholder="请重复输入密码"
                                               type="password">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="password_confirmation"> <span
                                                class="red">*</span> 用户类型:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <select name="type" class="form-control" id="type">
                                            @foreach(cons('user.type') as $val)
                                                <option value="{{ $val }}">{{ cons()->valueLang('user.type' , $val) }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="name"><span class="red">*</span>
                                        店家名称:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                                               value=""
                                               type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="username"><span class="red">*</span> 联系人:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="contact_person" name="contact_person"
                                               placeholder="请输入联系人" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="contact_info"><span class="red">*</span>
                                        联系方式:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="contact_info" name="contact_info"
                                               placeholder="请输入联系方式" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="contact_info"><span class="red">*</span>
                                        密保手机:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="" name="backup_mobile" placeholder="密保手机号码"
                                               type="text">

                                        <p class="prompt-item red">(作为密码找回重要依据和接收信息提醒,不可二次修改)</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="license_num"><span class="red">*</span>
                                        营业执照注册号:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="license_num" name="license_num"
                                               placeholder="请输入执照注册号" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
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

                                <div class="form-group">
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

                                <div class="form-group hidden">
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
                                    <label class="col-sm-3 control-label"><span class="red">*</span> 所在地:</label>

                                    <div class="col-sm-3">
                                        <select data-group="shop" name="address[province_id]"
                                                class="address-province form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-2 padding-clear">
                                        <select data-group="shop" name="address[city_id]"
                                                class="address-city form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select data-group="shop" name="address[district_id]"
                                                class="address-district form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-2  padding-clear">
                                        <select data-group="shop" name="address[street_id]"
                                                class="address-street form-control address"></select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="col-sm-3 control-label"><span class="red">*</span> 详细地址:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input type="hidden" name="address[area_name]"/>
                                        <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                                               class="form-control" value="">
                                        <input type="hidden" name="x_lng" value=""/>
                                        <input type="hidden" name="y_lat" value=""/>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-push-3">
                                        <p class="prompt-title prompt-item">(拖动图标可手动调整地图地址)</p>

                                        <div id="address-map" class="address-map margin-clear"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="spreading_code">推广码:</label>

                                    <div class="col-sm-9 col-md-6">
                                        <input class="form-control" id="spreading_code" name="spreading_code"
                                               placeholder="推广码(可选)" type="text">
                                    </div>
                                </div>
                                <div class="form-group hidden">
                                    <label class="col-sm-3 control-label" for="area">配送区域:</label>

                                    <div class="col-sm-9 col-md-8 padding-clear">
                                        <div class="col-sm-12">
                                            <a id="add-address" class="btn btn-default" href="javascript:" data-target="#addressModal"
                                               data-toggle="modal" data-loading-text="地址达到最大数量" >添加配送区域</a>
                                        </div>
                                        <div class="address-list col-lg-12">

                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-3">
                                        <button type="submit" class="btn btn-lg btn-warning btn-block btn-submit"
                                                data-loading-text="注册中..."
                                                data-done-text="注册成功" data-fail-text="注册失败">注册
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
@section('footer')
    <div class="footer">
        <footer class="panel-footer">
            <div class="container text-center text-muted">
                <div class="row">
                    <div class="col-sm-5 col-sm-push-2 text-left">
                        <p>Copyright {!! cons('system.company_name') !!}</p>

                        <p>{!! cons('system.company_record') !!}</p>
                    </div>
                    <div class="col-sm-6 text-left">
                        <p>联系方式：{{ cons('system.company_tel') . ' ' . cons('system.company_mobile') }}</p>

                        <p>联系地址：{{ cons('system.company_addr') }}</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@stop
@section('js-lib')
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
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
            $('select[name="type"]').change(function () {
                var type = $(this).val(),
                        wholesalerType = '{{ cons('user.type.wholesaler') }}',
                        agencyContract = $('span[name="agency_contract"]'),
                        addAddress = $('#add-address');
                if (type < wholesalerType) {
                    addAddress.prop('disabled' , true).closest('.form-group').addClass('hidden').find('.address-list').html('');
                    agencyContract.closest('.form-group').addClass('hidden').find('input[type="file"]').prop('disabled', true);
                } else {
                    if(type == wholesalerType) {
                        agencyContract.closest('.form-group').addClass('hidden').find('input[type="file"]').prop('disabled', true);
                    }else {
                        agencyContract.closest('.form-group').removeClass('hidden').find('input[type="file"]').prop('disabled', false);
                    }
                    addAddress.prop('disabled' , false).closest('.form-group').removeClass('hidden');
                }
            })
        })
    </script>
@stop
