@extends('auth.master')
@include('includes.cropper')
@include('includes.address', ['model' => 'shop'])

@section('title' , '注册 | 订百达')

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
                <div class="item">--------------</div>
                <div class="item item-text">
                    注册成功
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row register">
                    <div class="col-xs-12">
                        <form class="ajax-form form-horizontal" method="post"
                              action="{{  url('api/v1/auth/register')  }}"
                              accept-charset="UTF-8" data-done-url="{{ url('auth/reg-success') }}" autocomplete="off">
                            <fieldset>
                                <input type="hidden" name="user_name" value="{{ $user['user_name'] }}"/>
                                <input type="hidden" name="type" value="{{ $user['type'] }}"/>
                                <input type="hidden" name="backup_mobile" value="{{ $user['backup_mobile'] }}"/>
                                <input type="hidden" name="password" value="{{ $user['password'] }}"/>
                                <input type="hidden" name="password_confirmation"
                                       value="{{ $user['password_confirmation'] }}"/>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label" for="name"><span class="red">*</span>
                                        <span class="prompt">店家名称:</span></label>

                                    <div class="col-xs-9 col-md-4">
                                        <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                                               value=""
                                               type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label" for="username"><span
                                                class="red">*</span> <span
                                                class="prompt">联系人:</span></label>

                                    <div class="col-xs-9 col-md-4">
                                        <input class="form-control" id="contact_person" name="contact_person"
                                               placeholder="请输入联系人" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label" for="contact_info"><span class="red">*</span>
                                        <span class="prompt">联系方式:</span></label>

                                    <div class="col-xs-9 col-md-4">
                                        <input class="form-control" id="contact_info" name="contact_info"
                                               placeholder="请输入联系方式" type="text">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label" for="license_num">
                                        @if($user['type'] != cons('user.type.retailer'))
                                            <span class="red">*</span>
                                        @endif
                                        <span class="prompt">营业执照注册号:</span></label>

                                    <div class="col-xs-9 col-md-4">
                                        <input class="form-control" id="license_num" name="license_num"
                                               placeholder="请输入执照注册号" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="license">
                                        @if($user['type'] != cons('user.type.retailer'))
                                            <span class="red">*</span>
                                        @endif
                                            营业执照:</label>
                                    <div class="col-sm-9 col-md-4">
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
                                    <label class="col-sm-2 control-label" for="business_license"><span
                                                class="red">*</span> 食品流通许可证:</label>

                                    <div class="col-sm-9 col-md-4">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="business_license"
                                              class="btn btn-primary btn-sm fileinput-button">
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
                                    <label class="col-sm-2 control-label" for="username"><span class="red">*</span>
                                        代理合同:</label>

                                    <div class="col-sm-9 col-md-4">
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
                                    <label class="col-sm-2 control-label"><span class="red">*</span> 所在地:</label>

                                    <div class="col-sm-2">
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
                                    <label for="address" class="col-sm-2 control-label"><span class="red">*</span> 详细地址:</label>

                                    <div class="col-sm-9 col-md-4">
                                        <input type="hidden" name="address[area_name]"/>
                                        <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                                               class="form-control" value="">
                                        <input type="hidden" name="x_lng" value=""/>
                                        <input type="hidden" name="y_lat" value=""/>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-push-2">
                                        <p class="prompt-title prompt-item">(拖动图标可手动调整地图地址)</p>

                                        <div id="address-map" class="address-map margin-clear"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="spreading_code">推广码:</label>

                                    <div class="col-sm-9 col-md-4">
                                        <input class="form-control" id="spreading_code" name="spreading_code"
                                               placeholder="推广码(可选)" type="text">
                                    </div>
                                </div>
                                <div class="form-group hidden">
                                    <label class="col-sm-2 control-label" for="area">配送区域:</label>

                                    <div class="col-sm-9 col-md-6 padding-clear">
                                        <div class="col-sm-12">
                                            <a id="add-address" class="btn btn-default personal-add" href="javascript:"
                                               data-target="#addressModal"
                                               data-toggle="modal" data-loading-text="地址达到最大数量">添加配送区域</a>
                                        </div>
                                        <div class="address-list col-lg-12">

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-8 col-xs-offset-2">
                                        <button type="submit" class="btn  btn-warning  btn-submit"
                                                data-loading-text="注册中..."
                                                data-done-text="注册成功" data-fail-text="注册失败">提交
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

            var type = $('input[name="type"]').val(),
                wholesalerType = '{{ cons('user.type.wholesaler') }}',
                agencyContract = $('span[name="agency_contract"]'),
                addAddress = $('#add-address');
            if (type < wholesalerType) {
                addAddress.prop('disabled', true).closest('.form-group').addClass('hidden').find('.address-list').html('');
                agencyContract.closest('.form-group').addClass('hidden').find('input[type="file"]').prop('disabled', true);
            } else {
                if (type == wholesalerType) {
                    agencyContract.closest('.form-group').addClass('hidden').find('input[type="file"]').prop('disabled', true);
                } else {
                    agencyContract.closest('.form-group').removeClass('hidden').find('input[type="file"]').prop('disabled', false);
                }
                addAddress.prop('disabled', false).closest('.form-group').removeClass('hidden');
            }

        })
    </script>
@stop
