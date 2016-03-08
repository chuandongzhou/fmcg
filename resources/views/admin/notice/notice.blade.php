@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $notice->id ? 'put' : 'post' }}"
          action="{{ url('admin/notice/' . $notice->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">公告内容</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="title" name="title" placeholder="请输入公告内容"
                       value="{{ $notice->title }}">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $notice->id ? '修改' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop