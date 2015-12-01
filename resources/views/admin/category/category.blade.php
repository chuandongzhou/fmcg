@extends('admin.master')
@include('includes.cropper')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $category->id ? 'put' : 'post' }}"
          action="{{ url('admin/category/' . $category->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">

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
                <input class="form-control" type="text" placeholder="请输入分类名称" name="name" id="name"
                       value="{{ $category->name  }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="username">icon图标:</label>

            <div class="col-sm-10 col-md-6">
                <div class="progress collapse">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
                            <span data-name="icon"
                                  class="btn {{ $category->level == 1 || !$category->id ? 'btn-primary' : 'btn-cancel' }} btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file"
                                       {{ $category->level == 1 || !$category->id  ?  '' : 'disabled' }} accept="image/*"
                                       data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                <div class="image-preview w160">
                    <img src="{{ $category->icon_url }}" class="img-thumbnail">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-bg btn-primary" type="submit">{{ $category->id ? '保存' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('#pid').change(function(){
           if ($(this).val() == 0){
               $('.fileinput-button').removeClass('btn-cancel').addClass('btn-primary');
               $('input[name="file"]').prop('disabled' , false);
           }else{
               $('.fileinput-button').addClass('btn-cancel').removeClass('btn-primary');
               $('input[name="file"]').prop('disabled' , true);
           }
        })
    </script>
@stop