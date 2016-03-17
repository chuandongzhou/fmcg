@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/admin/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>用户ID</th>
                <th>用户密码</th>
                <th>昵称</th>
                <th>电话</th>
                <th>头像</th>
                <th>地址</th>
                <th>备注</th>
            </tr>
            </thead>
            <tbody>
            @foreach($userInfos as $user)
                <tr>
                    <td>{{ $user->userid }}</td>
                    <td>{{ $user->password }}</td>
                    <td>{{ $user->nick }}</td>
                    <td>{{ $user->mobile }}</td>
                    <td><img src="{{ $user->icon_url }}" width="40px" height="40px"> </td>
                    <td>{{ $user->address }}</td>
                    <td>{{ $user->remark }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
@stop