@extends('index.manage-master')
@section('subtitle', '客户')

@include('includes.uploader')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level"> 客户批量导入</span>
                </div>
            </div>
            <div class="row goods-editor">
                <form class="form-horizontal ajax-form" method="post" action="" autocomplete="off">
                    <div class="col-sm-12 editor-panel content-wrap">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">业务员:</label>

                            <div class="col-xs-3">
                                <select name="salesman_id" class="form-control">
                                    <option value="">请选择业务员</option>
                                    @foreach($salesmen as $id => $salesman)
                                        <option value="{{ $id }}">{{ $salesman }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group address-panel">
                            <label class="col-sm-2 control-label"><span class="red">*</span> 营业地址:</label>

                            <div class="col-sm-3">
                                <select data-group="business_address" name="province_id"
                                        class="address-province form-control address">
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select data-group="business_address" name="city_id"
                                        class="address-city form-control address">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select data-group="business_address" name="district_id"
                                        class="address-district form-control address">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select data-group="business_address" name="street_id"
                                        class="address-street form-control address"></select>
                            </div>
                            <div class="hidden address-text">
                                <input type="hidden" name="area_name" class="area-name"
                                       value=""/>
                                <input type="hidden" class="lng" name="x_lng"
                                       value=""/>
                                <input type="hidden" class="lat" name="y_lat"
                                       value=""/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label"></label>
                            <div class="col-sm-10 col-md-8">
                                <div data-group="business_address" class="baidu-map" id="business_address"
                                     data-lng=""
                                     data-lat=""
                                >
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">选择文件 :</label>

                            <div class="col-sm-6 padding-clear">
                                <div class="progress collapse">
                                    <div class="progress-bar progress-bar-striped active"></div>
                                </div>
                                <span class="btn btn-blue-lighter import-goods-btn">
                                    请选择Excel文件
                                    <input type="file"
                                           accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                           data-url="{{  url('api/v1/business/salesman-customer/import') }}"
                                           id="upload_file"
                                           name="file">
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">模板下载 : </label>
                            <div class="col-sm-10 padding-clear">
                                <a class="control-label btn-blue-lighter btn-sm templet-down"
                                   href="{{ url('business/salesman-customer/download-template') }}"><i
                                            class="fa fa-download"></i>
                                    模板下载</a>
                                <span class="prompt">请严格按照模板格式批量导入商品</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-push-2 col-sm-10   padding-clear">
                        <button class="btn btn-success save-btn">提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        var baiduMap = initMap();
        addressSelectChange(true, baiduMap);

        var customerImport = function () {
            $('#upload_file').change(function () {
                var fileName = $(this).val();
                var arr = fileName.split('\\');
                fileName = arr[arr.length - 1];
                $(this).closest('span').next('span').remove().end().after('<span>&nbsp;&nbsp;&nbsp;' + fileName + '</span>');
            }).fileupload({
                dataType: 'json',
                add: function (e, data) {
                    $(".save-btn").off('click').on('click', function () {
                        var obj = $('#upload_file');
                        obj.fileupload('disable');

                        var formData = {
                            'salesman_id' : $('select[name="salesman_id"]').val(),
                            'province_id' : $('select[name="province_id"]').val(),
                            'city_id' : $('select[name="city_id"]').val(),
                            'district_id' : $('select[name="district_id"]').val(),
                            'street_id' : $('select[name="street_id"]').val(),
                            'area_name' : $('input[name="area_name"]').val(),
                            'x_lng' : $('input[name="x_lng"]').val(),
                            'y_lat' : $('input[name="y_lat"]').val()
                        };
                        console.log(formData);

                        if (!formData.salesman_id) {
                            alert('请选择业务员');
                            return false
                        }
                        if (!formData.province_id || !formData.city_id || !formData.district_id) {
                            alert('地址必须包括省、市、区/县');
                            return false
                        }
                        if (!formData.x_lng || !formData.y_lat|| !formData.area_name) {
                            alert('地址错误，请刷新重试');
                            return false;
                        }
                        obj.parent().addClass('disabled').siblings('.progress').show();
                        obj.parent().siblings('.fileinput-error').remove();
                        $(this).html('<i class="fa fa-spinner fa-pulse"></i> 操作中...');
                        data.formData = formData;
                        data.submit();
                    });
                },
                fail: function (e, data) {
                    var json = data.jqXHR['responseJSON'], text = '文件上传失败';
                    if (json && json['message']) {
                        text = json['message'];
                    }
                    $(this).parent().after('<span class="fileinput-error"> ' + text + '</span>');
                },
                done: function (e, data) {
                    $(this).parent().after('<span class="fileinput-error"> 上传成功</span>');
                    location.href = site.url('business/salesman-customer');
                }, always: function (e, data) {
                    // 隐藏进度条并开放按钮
                    $(this).parent().removeClass('disabled').siblings('.progress').hide()
                        .children('.progress-bar').css('width', '0');
                    $(this).fileupload('enable');
                    $(".save-btn a").html('保存');
                },
                progressall: function (e, data) {
                    var progress = Math.round(data.loaded / data.total * 1000) / 10,
                        text = isNaN(progress) ? '100.0%' : (progress.toFixed(1) + '%');
                    $(this).parent().siblings('.progress')
                        .children('.progress-bar').css('width', text).html(text);
                }
            });
        }
        customerImport();
    </script>
@stop
