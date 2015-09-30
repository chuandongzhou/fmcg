@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="put"
          action="{{ url('admin/attr/' . $attr->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/attr') }}">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">标签名称</label>

            <div class="col-sm-4">
                <input class="form-control" type="text" placeholder="请输入标签名称" name="name" id="name"
                       value="{{ $attr->name  }}">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-bg btn-primary" type="submit">保存</button>
            </div>
        </div>
    </form>
@stop
