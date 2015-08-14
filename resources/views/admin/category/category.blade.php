@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $category->id ? 'put' : 'post' }}"
          action="{{ url('admin/category/' . $category->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">上级分类</label>

            <div class="col-sm-4">
                <select class="form-control" id="pid" name="pid">
                    <option value="0">作为一级菜单</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ $categories->data('id') === $category->pid ?'selected':'' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">分类名称</label>

            <div class="col-sm-4">
                <input class="form-control" type="text" placeholder="请输入分类名称" name="name" id="name" value="{{ $category->name  }}">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-bg btn-primary" type="submit">{{ $category->id ? '保存' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop