@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $promoter->id ? 'put' : 'post' }}"
          action="{{ url('admin/promoter/' . $promoter->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/promoter') }}">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">推广人员姓名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入姓名"
                       value="{{ $promoter->name }}">
            </div>
        </div>


        <div class="form-group">
            <label for="contact" class="col-sm-2 control-label">联系方式</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="contact" name="contact" placeholder="请输入联系方式"
                       value="{{ $promoter->contact }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $promoter->id ? '保存':'添加' }}</button>
            </div>
        </div>
    </form>
@stop