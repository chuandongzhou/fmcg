@extends('admin.master')

@include('includes.timepicker')

@section('subtitle' , '操作记录')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/operation/create') }}">运维管理</a>
        <a href="{{ url('admin/operation') }}">更新记录</a>
        <a href="javascript:" class="active">操作记录</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" method="get"
              action="{{ url('admin/operation/notification') }}" autocomplete="off">
            <input type="text" class="enter-control date datetimepicker" name="begin_day" data-format="YYYY-MM-DD"
                   placeholder="开始时间"
                   value="{{ $beginDay }}">
            至
            <input type="text" class="enter-control date datetimepicker" name="end_day" data-format="YYYY-MM-DD"
                   placeholder="结束时间"
                   value="{{ $endDay }}">
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="{{ url('admin/operation/notification-export?' . http_build_query(array_filter([ 'begin_day' => $beginDay,'end_day' => $endDay]))) }}"
               class="btn btn-border-blue control">导出</a>
        </form>
        <table class="table public-table table-bordered">
            <tr>
                <th>操作姓名</th>
                <th>账号</th>
                <th>ID</th>
                <th>操作时间</th>
                <th>操作内容</th>
            </tr>
            @foreach($notifications as $notification)
                <tr>
                    <td>{{ $notification->user->real_name }}</td>
                    <td>{{  $notification->user->name }}</td>
                    <td>{{ $notification->user_id }}</td>
                    <td>{{ $notification->created_at }}</td>
                    <td class="text-left">{!! $notification->content !!}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="text-right">
        {!! $notifications->appends(array_filter([ 'begin_day' => $beginDay,'end_day' => $endDay]))->render() !!}
    </div>
@stop
