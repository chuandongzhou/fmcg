@extends('index.menu-master')
@include('includes.uploader')
@include('includes.timepicker')
@section('subtitle', '个人中心-首页广告')

@section('right')
    <form class="form-horizontal ajax-form" method="{{ $advert->id ? 'put' : 'post' }}"
          action="{{ url('api/v1/personal/model/advert/' . $advert->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/model/advert') }}" autocomplete="off">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">广告名称</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名"
                       value="{{ $advert->name }}">
            </div>
        </div>
        <div class="form-group">
            <label for="upload-file" class="col-sm-2 control-label">广告图片</label>

            <div class="col-sm-4">
                <span data-name="image" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                <div class="image-preview w160">
                    <img src="{{ $advert->image_url }}" class="img-thumbnail">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">商品id</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="goods_id" name="goods_id" placeholder="请输入商品id"
                       value="{{ $advert->goods_id }}">
            </div>
        </div>

        <div class="form-group" id="date-time">
            <label class="col-sm-2 control-label">起止时间</label>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="start_at" placeholder="起始时间"
                       value="{{ $advert->start_at }}"/>
            </div>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="end_at" placeholder="结束时间"
                       value="{{ $advert->end_at }}"/>
            </div>
            <div class="col-sm-push-2 col-sm-10">
                <p class="help-block">结束时间为空时，表示广告永久有效。</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-success">{{ $advert->id ? '修改' : '添加' }}</button>
                <a href="javascript:history.go(-1)" class="btn btn-cancel">返回</a>
            </div>
        </div>
    </form>
    @parent
@stop
