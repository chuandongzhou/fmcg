@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $shopColumn->id ? 'put' : 'post' }}"
          action="{{ url('admin/shop-column/' . $shopColumn->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">栏目名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入栏目名"
                       value="{{ $shopColumn->name }}">
            </div>
        </div>


        <div class="form-group">
            <label for="id_list" class="col-sm-2 control-label">默认显示的id</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="id_list" name="id_list"
                       value="{{ $shopColumn->id_list ? implode('|' , $shopColumn->id_list) : '' }}"
                       placeholder="默认显示的id">( 按 '|' 隔开，最多10条)
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">排序</label>

            <div class="col-sm-4">
                @foreach(cons('sort.shop') as $key=>$sortName)
                    <label class="checks">
                        <input name="sort" value='{{ $key }}'
                               {{ $key == $shopColumn->sort ? 'checked' : '' }} type="radio">{{ cons()->valueLang('sort.shop' , $sortName) }}
                        &nbsp;
                    </label>
                @endforeach
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $shopColumn->id ? '修改' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop