@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/advert') }}" data-help-class="col-sm-push-2 col-sm-10">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">广告名称:</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名"
                       value="{{ $ad->name }}">
            </div>
        </div>
        <div class="form-group">
            <label for="upload-file" class="col-sm-2 control-label">广告图片:</label>

            <div class="col-sm-4">
                <button class="btn btn-primary btn-sm" id="upload-file" data-height="128" data-width="128"
                        data-target="#cropperModal" data-toggle="modal" type="button"> 本地上传(128x128)
                </button>
            </div>
        </div>

        @if($type=='retailer' || $type=='app')
            <div class="form-group">
                <label class="col-sm-2 control-label">广告类型:</label>

                <div class="col-sm-2">
                    <select class="form-control" name="add_type">
                        @foreach($ad_type as $key=>$item)
                            <option value="{{$key}}"
                            @if ($key == ($ad->time_type or ''))
                                    selected
                                @endif
                                >{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if($type=='app')
            <div class="form-group">
                <label class="col-sm-2 control-label">APP类型:</label>

                <div class="col-sm-2">
                    <select class="form-control" name="app_type">
                        @foreach($app_type as $key=>$item)
                            <option value="{{$key}}"
                            @if ($key == ($ad->time_type or ''))
                                    selected
                                @endif
                                >{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
        @endif
        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label">广告URL:</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="nickname" name="link_path" placeholder="请输入广告URL"
                       value="{{ $ad->link_path }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">有效时间:</label>

            <div class="col-sm-2">
                <select class="form-control" name="time_type">
                    @foreach($time_type as $key=>$item)
                        <option value="{{$key}}"
                        @if ($key == ($ad->time_type or ''))
                                selected
                            @endif
                            >{{ $item }}</option>
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