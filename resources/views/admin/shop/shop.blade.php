@extends('admin.master')
@include('includes.cropper')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="put"
          action="{{ url('admin/shop/'.$shop->id) }}" data-help-class="col-sm-push-2 col-sm-10">
        <div class="col-sm-9 user-show">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="username">店家LOGO:</label>

                <div class="col-sm-10 col-md-6">
                    <button class="btn btn-primary btn-sm" data-height="128" data-width="128"
                            data-target="#cropperModal" data-toggle="modal" data-name="logo" type="button">
                        本地上传(128x128)
                    </button>
                    <div class="image-preview">
                        <img class="img-thumbnail"
                             src="{{ is_object($shop->logo) ? upload_file_url($shop->logo->path) : asset('images/u8.png') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="username">店家名称:</label>

                <div class="col-sm-10 col-md-6">
                    <input class="form-control" id="name" name="name" placeholder="请输入店家名称"
                           value="{{ $shop->user->nickname }}"
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
                <label class="col-sm-2 control-label" for="username">联系方式:</label>

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
                        <img src="{{ is_object($shop->license) ? upload_file_url($shop->license->path) : '' }}"
                             class="img-thumbnail">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">图片</label>

                <div class="col-sm-10">
                    <button data-height="225" data-width="300" data-target="#cropperModal" data-toggle="modal"
                            data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                            id="shop-pic-upload">
                        请选择图片文件(300x225)
                    </button>
                    <div class="row shop-pictures">
                        <div class="hidden">
                            <input type="hidden" value="" name="images[id][]">
                            <input type="hidden" value="" name="images[path][]">
                            <input type="text" value="" name="images[name][]"
                                   class="form-control input-sm">
                        </div>
                        @foreach($shop->images as $image)
                            <div class="col-xs-3">
                                <div class="thumbnail">
                                    <button aria-label="Close" class="close" type="button"><span aria-hidden="true">×</span>
                                    </button>
                                    <img alt="" src="{{ upload_file_url($image->path) }}">
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
                <label class="col-sm-2 control-label">所在地</label>

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
                    <input type="text" placeholder="请输入详细地址" name="address" id="address" class="form-control"
                           value="{{ $shop->address }}">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="username">配送区域:</label>

                <div class="col-sm-10 col-md-8">
                    <div class="col-sm-12">
                        <a id="add-address" class="btn btn-default" href="javascript:" data-target="#addressModal"
                           data-toggle="modal" data-loading-text="地址达到最大数量">添加地址</a>
                    </div>
                    <div class="address-list col-lg-12">
                        @foreach (explode(',',$shop->delivery_area) as $area)
                            <div class="col-sm-12 fa-border">{{ $area }} <span class="close">×</span><input
                                        type="hidden" name="delivery_area[]" value="{{ $area }}"/></div>
                        @endforeach
                    </div>
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
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">选择要添加的地址<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body address-select">
                    <div>
                        <label class="control-label">&nbsp;&nbsp;&nbsp;所在地:</label>
                        <select class="address-province inline-control add-province">
                            <option selected="selected" value="">请选择省市/其他...</option>
                            <option value="210000">辽宁省</option>
                        </select>

                        <select class="address-city inline-control add-city">
                            <option selected="selected" value="">请选择城市...</option>
                            <option value="100">大连</option>
                        </select>

                        <select class="address-district inline-control add-district">
                            <option selected="selected" value="">请选择区/县...</option>
                            <option value="100">西港</option>
                        </select>

                        <div class="address-detail">
                            <label class="control-label">详细地址:</label>
                            <input type="text" placeholder="请输入详细地址" class="form-control address">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary btn-sm btn-add" data-text="添加">添加
                        </button>
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
            shopPicFunc();
            addAddFunc();
        })
    </script>
@stop