@extends('index.switch')
@include('includes.cropper')
@include('includes.address')
@section('subtitle', '个人中心-个人信息')

@section('container')
    <div class="container my-goods personal-center" id="container">
        <div class="row">
            @include('index.retailer-left')
            <div class="col-sm-10">
                <div class="col-sm-12 switching">
                    <a href="#" class="btn active">个人信息</a>
                    <a href="#" class="btn">收货地址</a>
                    <a href="#" class="btn">修改密码</a>
                </div>
                <form class="form-horizontal ajax-form" method="put"
                      action="{{ url('personal/retailer/information/') }}" data-help-class="col-sm-push-2 col-sm-10">
                    <div class="col-sm-9 user-show">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">店家图片:</label>

                            <div class="col-sm-10 col-md-6">
                                <button class="btn btn-primary btn-sm" data-height="100" data-width="100"
                                        data-target="#cropperModal" data-toggle="modal" data-name="logo" type="button">
                                    本地上传(100x100)
                                </button>
                                <div class="image-preview">
                                    <img class="img-thumbnail"
                                         src="{{ $shop->image_url }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">店家名称:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="nickname" name="name" placeholder="请输入店家名称"
                                       value="{{ $shop->name }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">联系人:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="contact_person" name="contact_person" placeholder="请输入联系人"
                                       value="{{ $shop->contact_person }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="contact_info">联系方式:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="contact_info" name="contact_info" placeholder="请输入联系方式"
                                       value="{{ $shop->contact_info }}"
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">店家简介:</label>

                            <div class="col-sm-10 col-md-6">
                            <textarea class="form-control" placeholder="请输入店家简介" rows="4" id="introduction"
                                      name="introduction">{{ $shop->introduction }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username">营业执照:</label>

                            <div class="col-sm-10 col-md-6">
                                <div class="progress collapse">
                                    <div class="progress-bar progress-bar-striped active"></div>
                                </div>
                            <span data-name="license" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件（可选）
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                                <div class="image-preview w160">
                                    <img src="{{ asset($shop->license->path) }}"
                                         class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">图片</label>

                            <div class="col-sm-10">
                                <button data-height="400" data-width="1000" data-target="#cropperModal" data-toggle="modal"
                                        data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                                        id="pic-upload">
                                    请选择图片文件(1000x400)
                                </button>
                                <div class="row pictures">
                                    @foreach($shop->images as $image)
                                        <div class="col-xs-3">
                                            <div class="thumbnail">
                                                <button aria-label="Close" class="close" type="button"><span
                                                        aria-hidden="true">×</span>
                                                </button>
                                                <img alt="" src="{{ asset($image->path)  }}">
                                                <input type="hidden" value="{{ $image->id }}" name="images[id][]">
                                                <input type="hidden" value="{{ $image->path }}" name="images[path][]">
                                                <input type="text" value="{{ $image->name }}" name="images[name][]"
                                                       class="form-control input-sm">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">店家地址 :</label>

                            <div class="col-sm-3">
                                <select name="province_id" class="address-province form-control">
                                    <option selected="selected" value="">请选择省市/其他...</option>
                                    <option value="210000">辽宁省</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="city_id" class="address-city form-control">
                                    <option selected="selected" value="">请选择城市...</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <select name="district_id" class="address-district form-control">
                                    <option selected="selected" value="">请选择区/县...</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-sm-2 control-label">详细地址</label>

                            <div class="col-sm-10 col-md-6">
                                <input type="text" placeholder="请输入详细地址" name="address" id="address" class="form-control">
                            </div>
                        </div>
                        <div class="item">
                            <label for="address" class="col-sm-2 control-label">地图标识 :</label>
                            <div class="col-sm-10 col-md-6">
                                <img src="http://placehold.it/300x250/CDF" alt="" title=""/>
                            </div>
                        </div>
                        <div class="col-sm-12 text-center save">
                            <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
                            <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i
                                    class="fa fa-reply"></i> 取消
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            picFunc();
        })
    </script>
@stop
