@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/user/save') }}" data-help-class="col-sm-push-2 col-sm-10">
        {{csrf_field()}}
        <div class="col-sm-12 user-show">
            <div class="top" style="height: 783px">
                <div class="top-left fa-border col-sm-6" style="height: 780px;">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">店家LOGO:</label>

                        <div class="col-sm-6">
                            <button class="btn btn-primary btn-sm" data-height="128" data-width="128"
                                    data-target="#cropperModal" data-toggle="modal" type="button"> 本地上传(128x128)
                            </button>
                            <div class="col-sm-8">
                                <img src="{{ asset('images/u8.png') }}" class="image-preview shop-img"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">店家名称:</label>

                        <div class="col-sm-6">
                            <input class="form-control" id="name" name="name" placeholder="请输入店家名称" value=""
                                   type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">联系人:</label>

                        <div class="col-sm-6">
                            <input class="form-control" id="contact_person" name="contact_person" placeholder="请输入联系人"
                                   value=""
                                   type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">联系方式:</label>

                        <div class="col-sm-6">
                            <input class="form-control" id="contact_info" name="contact_info" placeholder="请输入联系方式"
                                   value=""
                                   type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">店家简介:</label>

                        <div class="col-sm-6">
                            <textarea class="form-control" placeholder="请输入店家简介" rows="4" id="introduction"
                                      name="introduction"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">营业执照:</label>

                        <div class="col-sm-6">
                            <button class="btn btn-primary btn-sm" data-height="128" data-width="128"
                                    data-target="#cropperModal" data-toggle="modal" type="button"> 本地上传(128x128)
                            </button>
                            <div class="col-sm-8">
                                <img src="{{ asset('images/u8.png') }}" class="image-preview shop-img"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">门店图片:</label>

                        <div class="col-sm-6">
                            <button class="btn btn-primary btn-sm" data-height="128" data-width="128"
                                    data-target="#cropperModal" data-toggle="modal" type="button"> 本地上传(128x128)
                            </button>

                            <div class="col-sm-10 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-10 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-10 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-10 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-10 fa-border shop-img">图片一<span>x</span></div>
                        </div>
                    </div>
                </div>
                <div class="top-right col-sm-6 fa-border" style="height: 780px;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">地址:</label>

                        <div class="col-sm-2 address">
                            <select class="form-control" name="province_id">
                                <option value="0">省</option>
                                <option value="1">四川</option>
                            </select>
                        </div>
                        <div class="col-sm-2 address">
                            <select class="form-control" name="city_id">
                                <option value="0">市</option>
                                <option value="11">成都</option>
                            </select>
                        </div>
                        <div class="col-sm-2 address">
                            <select class="form-control" name="district_id">
                                <option value="0">区</option>
                                <option value="111">高新区</option>
                            </select>
                        </div>
                        <div class="col-sm-4 address">
                            <select class="form-control" name="street_id">
                                <option value="0">街道</option>
                                <option value="1111">天府五街</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label"></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="address" name="address" placeholder="请输入详细地址"
                                   value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="padding-left: 50px;">
                            <img src="{{ asset('images/map.png')  }}" width="327px" height="208px"/>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="username">配送区域:</label>

                        <div class="col-sm-8">
                            <div class="col-sm-12">
                                <a class="btn btn-default" href="javascript:">添加地址</a>
                            </div>
                            <div class="col-sm-12 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-12 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-12 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-12 fa-border shop-img">图片一<span>x</span></div>
                            <div class="col-sm-12 fa-border shop-img">图片一<span>x</span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="padding-left: 50px;">
                            <img src="{{ asset('images/map1.png')  }}" width="327px" height="243px";/>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bottom fa-border" style="height: 290px">
                <div class="col-sm-6">
                    <label class="col-sm-3 control-label" for="username">门店图片预览:</label>
                </div>
                <div class="col-sm-12 phone-display">
                    <img src="{{ asset('images/u46.png') }}" height="150px" width="165px">
                    <img src="{{ asset('images/u46.png') }}" height="150px" width="165px">
                    <img src="{{ asset('images/u46.png') }}" height="150px" width="165px">
                    <img src="{{ asset('images/u46.png') }}" height="150px" width="165px">
                    <img src="{{ asset('images/u46.png') }}" height="150px" width="165px">
                </div>
                <div class="col-sm-12 text-center save">
                    <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
                    <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i class="fa fa-reply"></i> 取消</button>
                </div>
            </div>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

        })
    </script>
@stop