@extends('admin.master')

@section('subtitle' , '更新记录')

@section('right-container')

    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/operation-record/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>终端类型</th>
                <th>版本号</th>
                <th>版本名</th>
                <th>更新内容</th>
                <th>操作时间</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ array_flip(cons('push_device'))[$record->type] }}</td>
                    <td>{{ $record->version_no }}</td>
                    <td>{{ $record->version_name }}</td>
                    <td>{{ mb_strlen($record->content)>30?mb_substr($record->content,0,30).'...': $record->content }}</td>
                    <td>{{ $record->created_at }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/version-record/' . $record->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </a>
                            <a type="button" class="btn btn-success ajax disabled" data-method="delete"
                               data-url="#">
                                <i class="fa fa-trash-o"></i> 编辑
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
