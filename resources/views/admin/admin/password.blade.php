@extends('admin.master')


@section('right-container')
    <form class="form-horizontal ajax-form" method="put"
          action="{{ !isset($pay) ? url('admin/admin/password') : url('admin/admin/pay-password') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">

        <div class="form-group">
            <label for="old-password" class="col-sm-2 control-label">旧密码:</label>

            <div class="col-sm-4">
                <input type="password" class="form-control" id="old-password" name="old_password" placeholder="请输入旧密码">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">新密码:</label>

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
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">修改</button>
            </div>
        </div>
    </form>
@stop