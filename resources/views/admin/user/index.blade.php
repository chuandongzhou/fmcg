@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/admin/') }}" data-help-class="col-sm-push-2 col-sm-10">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>用户名</th>
                <th>昵称</th>
                <th>注册时间</th>
                <th>状态</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <th scope="row"><input type="checkbox" class="child" name="uid[]" value="{{ $user->id }}"/></th>
                    <td>{{ $user->user_name }}</td>
                    <td>{{ $user->nickname }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ cons()->valueLang('status', $user->status) }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/user/' . $user->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a class="btn btn-default" href="{{ url('admin/shop/' . $user->shop['id'] . '/edit') }}">
                                <i class="fa fa-user"></i> 查看
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/user/' . $user->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/user/batch') }}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax" data-method="put" data-data='{"status":1}'
                    data-url="{{ url('admin/user/switch') }}">
                <i class="fa fa-adjust"></i> 启用
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="put" data-data='{"status":0}'
                    data-url="{{ url('admin/user/switch') }}">
                <i class="fa fa-trash-o"></i> 禁用
            </button>
        </div>
    </form>
    {!! $users->render() !!}
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop