@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $user->id ? 'put' : 'post' }}"
          action="{{ url('admin/user/' . $user->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">用户名</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="username" name="user_name" placeholder="请输入用户名"
                       value="{{ $user->user_name }}">
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">密保手机</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="backup_mobile" name="backup_mobile" placeholder="请输入密保手机"
                       value="{{ $user->backup_mobile }}">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密码</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">重复密码</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password-confirmation" name="password_confirmation"
                       placeholder="请重复输入密码">
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" name="type" value="{{ $typeId }}">
                <button type="submit" class="btn btn-bg btn-primary">{{ $user->id ? '修改' : '添加' }}</button>
            </div>
        </div>
    </form>
@stop
