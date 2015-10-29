@extends('master')
@include('includes.cropper')
@include('includes.address')

@section('title' , '注册')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-2">
                <div class="panel panel-auth">
                    <div class="panel-body">
                        <form class="ajax-form form-horizontal" method="post"
                              action="{{  url('api/v1/auth/register')  }}"
                              accept-charset="UTF-8" data-done-then="referer">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="user_name">用户名:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="user_name" name="user_name" placeholder="请输入用户名"
                                               type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="password">密码:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="password" name="password" placeholder="请输入密码"
                                               type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="password_confirmation">确认密码:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="password_confirmation"
                                               name="password_confirmation" placeholder="请重复输入密码"
                                               type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="password_confirmation">类型:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <select name="type" class="form-control" id="type">
                                            @foreach(cons('user.type') as $val)
                                                <option value="{{ $val }}">{{ cons()->valueLang('user.type' , $val) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="name">店家名称:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                                               value=""
                                               type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="username">联系人:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="contact_person" name="contact_person"
                                               placeholder="请输入联系人" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="contact_info">联系方式:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="contact_info" name="contact_info"
                                               placeholder="请输入联系方式" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="spreading_code">推广码:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="spreading_code" name="spreading_code"
                                               placeholder="请输入推广码" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="license_num">营业执照注册号:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input class="form-control" id="license_num" name="license_num"
                                               placeholder="请输入执照注册号" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="license">营业执照:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="license" class="btn btn-primary btn-sm fileinput-button">
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
                                    <label class="col-sm-2 control-label" for="username">食品流通许可证:</label>

                                    <div class="col-sm-10 col-md-6">
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

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="username">代理合同:</label>

                                    <div class="col-sm-10 col-md-6">
                                        <div class="progress collapse">
                                            <div class="progress-bar progress-bar-striped active"></div>
                                        </div>
                                        <span data-name="agency_contract"
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
                                <div class="form-group shop-address">
                                    <label class="col-sm-2 control-label">所在地</label>

                                    <div class="col-sm-3">
                                        <select data-group="shop" name="address[province_id]"
                                                class="address-province form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <select data-group="shop" name="address[city_id]"
                                                class="address-city form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select data-group="shop" name="address[district_id]"
                                                class="address-district form-control address">
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select data-group="shop" name="address[street_id]"
                                                class="address-street form-control address"></select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="col-sm-2 control-label">详细地址</label>

                                    <div class="col-sm-10 col-md-6">
                                        <input type="hidden" name="address[area_name]"/>
                                        <input type="text" placeholder="请输入详细地址" name="address[address]" id="address"
                                               class="form-control" value="">
                                        <input type="hidden" name="x_lng" value=""/>
                                        <input type="hidden" name="y_lat" value=""/>
                                        <div id="address-map" style="margin-top:20px;overflow: hidden;zoom: 1;position: relative;width: 100%;height: 200px;"></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-lg btn-block btn-submit" data-loading-text="注册中..."
                                        data-done-text="注册成功" data-fail-text="注册失败">注册
                                </button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('footer')
    <footer class="panel-footer login-footer">
        <div class="container text-center text-muted">&copy;2003-2015 版权所有</div>
    </footer>
    @parent
@stop
@section('js-lib')
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
                $('input[name="address[area_name]"]').val(provinceVal + ' ' + cityVal + ' ' + districtVal + ' ' + streetVal);
            })
        })
    </script>
@stop
