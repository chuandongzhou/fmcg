@extends('admin.master')

@section('right-container')
<form class="form-horizontal ajax-form" method="post"
      action="{{ url('admin/advert') }}" data-help-class="col-sm-push-2 col-sm-10">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">广告名称:</label>

        <div class="col-sm-4">
            <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名" />
        </div>
    </div>
    <div class="form-group">
        <label for="upload-file" class="col-sm-2 control-label">广告图片:</label>
        <div class="col-sm-4">
                <span data-name="image" class="btn btn-primary btn-sm fileinput-button">
                    请选择图片文件
                    <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}" name="file">
                </span>

            <div class="image-preview w160">
                <img style="width:100px;height:100px;margin-bottom: 0;" src="" class="img-thumbnail">
            </div>
        </div>
    </div>

    @if($type == 'retailer' || $type == 'app')
    <div class="form-group">
        <label class="col-sm-2 control-label">广告类型:</label>

        <div class="col-sm-2">
            <select class="form-control" name="add_type">
                @foreach($ad_type as $key=>$item)
                <option value="{{$key}}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @if($type == 'app')
    <div class="form-group">
        <label class="col-sm-2 control-label">APP类型:</label>

        <div class="col-sm-2">
            <select class="form-control" name="app_type">
                @foreach($app_type as $key=>$item)
                <option value="{{$key}}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
    @endif
    <div class="form-group">
        <label for="nickname" class="col-sm-2 control-label">广告URL:</label>

        <div class="col-sm-4">
            <input type="text" class="form-control" id="nickname" name="link_path" placeholder="请输入广告URL" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">有效时间:</label>

        <div class="col-sm-2">
            <select class="form-control" name="time_type">
                @foreach($time_type as $key=>$item)
                <option value="{{$key}}">{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-bg btn-primary">添加</button>
        </div>
    </div>
</form>
@stop

@include('includes.uploader')