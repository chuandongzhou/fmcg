@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/promoter/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" id="parent"/></th>
                <th>推广人员姓名</th>
                <th>联系方式</th>
                <th>推广码</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promoters as $promoter)
                <tr>
                    <th scope="row"><input type="checkbox" class="child" name="ids[]" value="{{ $promoter->id }}"/></th>
                    <td>{{ $promoter->name }}</td>
                    <td>{{ $promoter->contact }}</td>
                    <td>{{ $promoter->spreading_code }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/promoter/' . $promoter->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/promoter/' . $promoter->id) }}">
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
                    data-url="{{ url('admin/promoter/batch') }}">
                <i class="fa fa-trash-o"></i> 删除
            </button>
        </div>
    </form>
    {!! $promoters->render() !!}
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop