@extends('admin.master')

@section('subtitle' , '运维操作记录')

@include('includes.timepicker')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $record->id ? 'put' : 'post' }}"
          action="{{ url('admin/operation-record/' . $record->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/operation-record') }}" autocomplete="off">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">操作人姓名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入姓名"
                       value="{{ $record->name }}">
            </div>
        </div>


        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">开始时间</label>

            <div class="col-sm-4">
                <input type="text" class="form-control datetimepicker" id="start_at" name="start_at"
                       value="{{ \Carbon\Carbon::now()->subHour() }}">
            </div>
        </div>
        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">结束时间</label>

            <div class="col-sm-4">
                <input type="text" class="form-control datetimepicker" id="end_at" name="end_at"
                       value="{{ \Carbon\Carbon::now() }}">
            </div>
        </div>

        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">操作原因</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="reason" name="reason" placeholder="请输入操作原因"
                       value="{{ $record->reason }}">
            </div>
        </div>
        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">操作内容</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="content" name="content" placeholder="请输入操作内容"
                       value="{{ $record->content }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $record->id ? '保存':'添加' }}</button>
            </div>
        </div>
    </form>
@stop
