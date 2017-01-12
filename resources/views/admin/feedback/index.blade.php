@extends('admin.master')

@include('includes.timepicker')

@section('subtitle' , '用户反馈管理')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="javascript:" class="active" >意见反馈</a>
        <a href="{{ url('admin/trade') }}">支付结果查询</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" method="get" action="{{ url('admin/feedback') }}" autocomplete="off">
            <input type="text" class="enter-control date datetimepicker" name="begin_day" data-format="YYYY-MM-DD" placeholder="开始时间"
                   value="{{ $beginDay or '' }}">
            至
            <input type="text" class="enter-control date datetimepicker" name="end_day" data-format="YYYY-MM-DD" placeholder="结束时间"
                   value="{{ $endDay or '' }}">
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a class="btn btn-border-blue control">导出</a>
        </form>
        <table class="table public-table table-bordered">
            <tr>
                <th>账号</th>
                <th>时间</th>
                <th>联系方式</th>
                <th>反馈内容</th>
                <th>处理状态</th>
                <th>操作</th>
            </tr>
            @foreach($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->account }}</td>
                    <td>{{ $feedback->created_at }}</td>
                    <td>{{ $feedback->contact }}</td>
                    <td>{{ $feedback->content }}</td>
                    <td>{{ cons()->valueLang('feedback.status', $feedback->status) }}</td>

                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            @if ($feedback->status == 0)
                                <a type="button" class="btn edit  ajax" data-method="post"
                                   data-data='{"id":{{ $feedback->id }}}'
                                   data-url="{{ url('admin/feedback/handle') }}">
                                    <i class="iconfont icon-xiugai"></i> 处理
                                </a>
                            @endif
                            <a type="button" class="btn remove ajax" data-method="delete"
                               data-data='{"id":{{ $feedback->id }}}'
                               data-url="{{ url('admin/feedback/destroy') }}">
                                <i class="iconfont icon-shanchu"></i> 删除
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="right">
        {!! $feedbacks->render() !!}
    </div>

@stop
