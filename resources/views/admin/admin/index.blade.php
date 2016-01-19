@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/admin') }}" data-help-class="col-sm-push-2 col-sm-10"  autocomplete="off">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>管理员ID</th>
                <th>管理员账号</th>
                <th>姓名</th>
                <th>所属角色</th>
                <th>状态</th>
                <th class="text-nowrap">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
                <tr>
                    <td><input type="checkbox" class="child" name="ids[]" value="{{$admin->id}}"/> </td>
                    <td>{{$admin->id}}</td>
                    <td>{{$admin->name}}</td>
                    <td>{{$admin->real_name}}</td>
                    <td>{{$admin->role->name}}</td>
                    <td>
                        {{ $admin->status ? '启用' : '禁用' }}
                    </td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/admin/'.$admin->id.'/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <button type="button" class="btn btn-danger ajax" data-method="delete"
                                    data-url="{{ url('admin/admin/'.$admin->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        <div class="btn-group btn-group-xs" role="group">
            <input type="checkbox" id="parent" class="checkbox-inline"/>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/admin/batch') }}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax" data-method="put" data-data='{"status":1}'
                    data-url="{{ url('admin/admin/switch') }}">
                <i class="fa fa-adjust"></i> 启用
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="put" data-data='{"status":0}'
                    data-url="{{ url('admin/admin/switch') }}">
                <i class="fa fa-trash-o"></i> 禁用
            </button>
        </div>
    </form>
    {!! $admins->render() !!}
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop