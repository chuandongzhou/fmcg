@extends('admin.master')

@section('subtitle' , '运维操作记录')

@include('includes.timepicker')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/version-record') }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/version-record') }}" autocomplete="off">

        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">终端类型</label>

            <div class="col-sm-4">
                <select name="type" class="form-control">
                    @foreach(cons()->valueLang('push_device') as $key => $item)
                        <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="version_no" class="col-sm-2 control-label">版本号</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="version_no" name="version_no" placeholder="请输入版本号" />
            </div>
        </div>
        <div class="form-group">
            <label for="version_name" class="col-sm-2 control-label">版本名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="version_name" name="version_name" placeholder="请输入版本名" />
            </div>
        </div>
        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">更新内容</label>

            <div class="col-sm-4">
                <textarea class="form-control" rows="8" id="content" name="content" placeholder="请输入更新内容"></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">添加</button>
            </div>
        </div>
    </form>
@stop
