@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $homeColumn->id ? 'put' : 'post' }}"
          action="{{ url('admin/column/' . $homeColumn->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">栏目名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入栏目名"
                       value="{{ $homeColumn->name }}">
            </div>
        </div>

        <div class="form-group">
            <label for="id_list" class="col-sm-2 control-label">默认显示的id</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="id_list" name="id_list" value="{{ $homeColumn->id_list }}"
                       placeholder="默认显示的id">( 按 '|' 隔开，最多10条)
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">排序</label>

            <div class="col-sm-4">
                @foreach(cons('sort.' . $type) as $key=>$sortName)
                    <label class="checks">
                        <input name="sort" value='{{ $key }}' {{ $key == $homeColumn->sort ? 'checked' : '' }} type="radio">{{ cons()->valueLang('sort.' . $type , $sortName) }} &nbsp;
                    </label>
                @endforeach
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" name="type" value="{{ $typeId }}">
                <button type="submit" class="btn btn-bg btn-primary">{{ $homeColumn->id ? '修改' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop