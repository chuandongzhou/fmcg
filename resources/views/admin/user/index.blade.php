@extends('admin.master')

@section('right-container')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>用户名</th>
            <th>昵称</th>
            <th>地址</th>
            <th>状态</th>
            <th class="text-nowrap">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->user_name }}</td>
                <td>{{ $user->nickname }}</td>
                <td>{{ $user->address }}</td>
                <td>{{ cons()->valueLang('status', $user->status) }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <a class="btn btn-primary" href="{{ url('admin/user/' . $user->id . '/edit') }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <button type="button" class="btn btn-danger ajax" data-method="delete"
                                data-url="{{ url('admin/user/' . $user->id) }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $users->render() !!}
@stop