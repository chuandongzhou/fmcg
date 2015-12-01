@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/operation-record/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        {{csrf_field()}}
        <table class="table table-striped">
            <thead>
            <tr>
                <th>操作人</th>
                <th>开始时间</th>
                <th>结束时间</th>
                <th>操作原因</th>
                <th>操作内容</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->start_at }}</td>
                    <td>{{ $record->end_at }}</td>
                    <td>{{ $record->reason }}</td>
                    <td>{{ $record->content }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/operation-record/' . $record->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
    {!! $records->render() !!}
@stop
