@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content table-responsive">
        <form method="post" action="{{url('admin/admin/change-password')}}">
            旧密码：<input type="password" name="old_password" />
            新密码：<input type="password" name="new_password" />
            密码确认：<input type="password" name="new_password_confirmation" />
            <button type="submit" >禁用</button>
        </form>
    </div>
@stop