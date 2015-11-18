@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="put"
          action="{{ url('admin/admin/'.$user->id) }}" data-help-class="col-sm-push-2 col-sm-10">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">管理员账号:</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="username" name="user_name" placeholder="请输入用户名"
                       value="{{ $user->name }}">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">管理员密码:</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirmation" class="col-sm-2 control-label">确认密码:</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="password-confirmation" name="password_confirmation"
                       placeholder="请重复输入密码">
            </div>
        </div>

        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label">管理员姓名:</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="nickname" name="real_name" placeholder="请输入姓名"
                       value="{{ $user->real_name }}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">所属角色:</label>

            <div class="col-sm-2">
                <select class="form-control" name="role_id">
                    @foreach($role as $key=>$item)
                        <option value="{{$key}}" {{ $key == $user->role->id ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">修改</button>
                <a href="{{ url('admin/admin') }}" class="btn btn-bg btn-primary">取消</a>
            </div>
        </div>
    </form>
@stop
