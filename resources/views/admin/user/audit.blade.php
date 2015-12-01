@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/user/multi_audit') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>用户名</th>
                <th>昵称</th>
                <th>账号类别</th>
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
                    <td>{{ $user->shop ? $user->shop->name : '' }}</td>
                    <td>{{ cons()->valueLang('user.type', $user->type) }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ cons()->valueLang('user.audit_status', $user->audit_status) }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-default" href="{{ url('admin/shop/' . $user->shop['id'] . '/edit') }}">
                                <i class="fa fa-user"></i> 查看
                            </a>
                            <a class="btn btn-primary ajax" data-method="put"
                               data-url="{{ url('admin/user/audit/' . $user->id) }}"
                               data-data='{"status":"{{ cons('user.audit_status.pass') }}"}'
                            >
                                <i class="fa fa-check"></i> 审核通过
                            </a>
                            @if($user->audit_status != cons('user.audit_status.not_pass'))
                                <a type="button" class="btn btn-danger ajax" data-method="put"
                                   data-url="{{ url('admin/user/audit/' . $user->id) }}"
                                   data-data='{"status":"{{ cons('user.audit_status.not_pass') }}"}'>
                                    <i class="fa fa-close"></i> 审核不通过
                                </a>
                            @endif
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
            <button type="button" class="btn btn-primary ajax" data-method="put"
                    data-data={"status":"{{ cons('user.audit_status.pass') }}"}>
                <i class="fa fa-adjust"></i> 批量审核通过
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="put"
                    data-data={"status":"{{ cons('user.audit_status.not_pass') }}"}>
                <i class="fa fa-trash-o"></i> 批量审核不通过
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