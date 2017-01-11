@extends('admin.master')

@include('includes.timepicker')

@section('subtitle' , '用户管理')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/operation/create') }}">运维管理</a>
        <a href="javascript:" class="active">更新记录</a>
        <a href="{{ url('admin/operation/notification') }}">操作记录</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" method="get"
              action="{{ url('admin/operation') }}" autocomplete="off">
            <input type="text" class="enter-control date datetimepicker" name="begin_day" data-format="YYYY-MM-DD" placeholder="开始时间"
                   value="{{ $beginDay }}">
            至
            <input type="text" class="enter-control date datetimepicker" name="end_day" data-format="YYYY-MM-DD" placeholder="结束时间"
                   value="{{ $endDay }}">
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="{{ url("admin/operation/export?begin_day={$beginDay}&end_day={$endDay}") }}" class="btn btn-border-blue control">导出</a>
        </form>
        <table class="table public-table table-bordered">
            <tr>
                <th>终端类型</th>
                <th>更新时间</th>
                <th>版本号</th>
                <th>版本名称</th>
                <th>更新内容</th>
                <th>操作</th>
            </tr>
            @foreach($records as $record)
                <tr>
                    <td>{{ cons()->valueLang('push_device' , $record->type) }}</td>
                    <td>{{ $record->created_at }}</td>
                    <td>{{ $record->version_no }}</td>
                    <td width="20%">{{ $record->version_name }}</td>
                    <td width="20%">{{ $record->content }}</td>
                    <td>
                        {{--<a class="edit" href="javascript:;" data-target="#myModal" data-toggle="modal"><i--}}
                        {{--class="iconfont icon-xiugai"></i> 编辑</a>--}}
                        <a class="remove btn ajax" href="javascript:"
                           data-url="{{ url('admin/operation/' . $record->id) }}"
                           data-method="delete"><i class="iconfont icon-shanchu"></i> 删除</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="text-right">
        {{ $records->render() }}
    </div>
@stop
