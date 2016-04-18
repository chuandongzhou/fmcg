@extends('admin.master')

@include('includes.timepicker')

@section('subtitle' , '用户反馈管理')

@section('right-container')
    <form class="form-horizontal" method="get" action="{{ url('admin/feedback') }}" autocomplete="off">
        <div class="form-group">
            <label class="col-sm-1 control-label" for="feed_time">时间</label>
            <input type="text" class="datetimepicker inline-control" value="{{ $feedTime }}" data-format="YYYY-MM-DD" name="feed_time">
            <input type="submit" class="btn btn-default" value="搜索">
        </div>
    </form>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>提交者账号</th>
            <th>联系方式</th>
            <th>提交信息内容</th>
            <th>处理状态</th>
            <th class="text-nowrap">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($feedbacks as $feedback)
            <tr>
                <td>{{ $feedback->account }}</td>
                <td>{{ $feedback->contact }}</td>
                <td>{{ $feedback->content }}</td>
                <td>{{ cons()->valueLang('feedback.status', $feedback->status) }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        @if ($feedback->status == 0)
                            <a type="button" class="btn btn-primary  ajax" data-method="post"
                               data-data='{"id":{{ $feedback->id }}}'
                               data-url="{{ url('admin/feedback/handle') }}">
                                <i class="fa fa-user"></i> 处理
                            </a>
                        @endif
                        <a type="button" class="btn btn-danger ajax" data-method="delete"
                           data-data='{"id":{{ $feedback->id }}}'
                           data-url="{{ url('admin/feedback/destroy') }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {!! $feedbacks->render() !!}
@stop
