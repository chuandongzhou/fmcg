@extends('admin.master')

@section('subtitle' , '公告管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/notice') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>公告标题</th>
                <th>公告内容</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($notices as $notice)
                <tr>
                    <td>{{ $notice->title }}</td>
                    <td>
                        <div style="text-overflow: ellipsis; white-space: nowrap;  overflow: hidden; width:600px">
                            {{ $notice->content }}
                        </div>
                       </td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/notice/' . $notice->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/notice/' . $notice->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
@stop