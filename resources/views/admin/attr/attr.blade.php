@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $attr->id ? 'put' : 'post' }}"
          action="{{ url('admin/attr/' . $attr->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/attr') }}">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">所属分类</label>

            <div class="col-sm-4">
                <select class="form-control" id="category_id" name="category_id">
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}"{{ $categories->data('level') == 1 ? ' disabled' : '' }}{{ $id == $attr->category_id  ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">所属标签</label>

            <div class="col-sm-4">
                <select class="form-control" id="pid" name="pid">
                    <option value="0">作为主标签</option>
                    @foreach($first_category_of_attr as $id => $name)
                        <option value="{{ $id }}" {{ $id == $attr->pid  ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">标签名称</label>

            <div class="col-sm-4">
                <input class="form-control" type="text" placeholder="请输入标签名称" name="name" id="name"
                       value="{{ $attr->name  }}">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-bg btn-primary" type="submit">{{ $attr->id ? '保存' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script>
        $(function () {
            $('#category_id').change(function () {
                var categoryId = $(this).val();
                $.get(site.api('category/' + categoryId + '/attrs'), {'pid': 0}, function (data) {
                    var html = '<option value="0">作为主标签</option>';
                    for (var index in data) {
                        html += '<option value="' + index + '">' + data[index] + '</option>';
                    }
                    $('#pid').html(html);
                }, 'json')
            })
        });
    </script>
@stop