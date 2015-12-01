@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/app-url') }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('admin/version-record') }}" autocomplete="off">
        <div class="form-group">
            <label for="android_url" class="col-sm-2 control-label">android下载地址</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="android_url" name="android_url" placeholder="请输入android下载地址" value="{{ $androidUrl or '' }}" />
            </div>
        </div>
        <div class="form-group">
            <label for="ios_url" class="col-sm-2 control-label">ios下载地址</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="ios_url" name="ios_url" placeholder="请输入ios下载地址" value="{{ $androidUrl or '' }}"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">保存</button>
            </div>
        </div>
    </form>

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
