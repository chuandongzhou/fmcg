@extends('admin.master')

@section('right-container')
    <form class="form-horizontal ajax-form" method="put"
          action="{{url('admin/admin/')}}" data-help-class="col-sm-push-2 col-sm-10">
        {{csrf_field()}};
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
                    <td><input type="checkbox" name="status[]" value="{{$admin->id}}"/> </td>
                    <td>{{$admin->id}}</td>
                    <td>{{$admin->user_name}}</td>
                    <td>{{$admin->real_name}}</td>
                    <td>{{$admin->role->name}}</td>
                    <td>
                        @if($admin->status)
                            禁用
                        @else
                            启用
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{url('admin/admin/'.$admin->id.'/edit')}}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <button type="button" class="btn btn-danger ajax" data-method="delete"
                                    data-url="{{url('admin/admin/'.$admin->id)}}">
                                <i class="fa fa-trash-o"></i> 删除
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

        <input type="checkbox" id="check-all">

        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{url('admin/admin/delete-batch')}}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-primary ajax" data-method="input"
                    data-url="{{url('admin/admin')}}">
                <i class="fa fa-adjust"></i> 启用
            </button>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="input"
                    data-url="{{url('admin/admin')}}">
                <i class="fa fa-trash-o"></i> 禁用
            </button>
        </div>
    </form>
    {!! $admins->render() !!}
@stop
@section('js')
    @parent
    <script>
        $('#check-all').on('click',function(){
            var checkedOfAll=$(this).prop("checked");
            $(':checkbox').prop("checked", checkedOfAll);
        });
    </script>
@stop