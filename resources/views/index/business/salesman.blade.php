@extends('index.menu-master')

@section('subtitle', '业务管理-业务员添加')

@section('right')
    <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/salesman/' . $salesman->id) }}"
          method="{{ $salesman->id ? 'put' : 'post' }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('business/salesman') }}" autocomplete="off">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="username">头像:</label>

            <div class="col-sm-10 col-md-6">
                <button class="btn btn-primary btn-sm" data-height="100" data-width="100"
                        data-target="#cropperModal" data-toggle="modal" data-name="avatar" type="button">
                    本地上传(100x100)
                </button>
                <div class="image-preview">
                    <img class="img-thumbnail"
                         src="{{ $salesman->avatar_url }}">
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="account">账号:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="account" name="account" placeholder="请输入业务员账号"
                       value="{{ $salesman->account }}"
                       type="text">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 control-label" for="password">密码:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="password" name="password" placeholder="请输入密码"
                       value="{{ $salesman->password }}"
                       type="password">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="password_conformation">确认密码:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="password_conformation" name="password_conformation"
                       placeholder="请重复输入密码"
                       value="{{ $salesman->password }}"
                       type="password">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 control-label" for="name">名称:</label>

            <div class="col-sm-10 col-md-6">
                <input class="form-control" id="name" name="name" placeholder="请输入业务员名称"
                       value="{{ $salesman->password }}"
                       type="password">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-push-2 col-sm-10">
                <button class="btn btn-primary" type="submit">提交</button>
            </div>
        </div>
    </form>
    @parent
@stop
