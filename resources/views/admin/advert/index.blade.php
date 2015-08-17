@extends('admin.master')

@section('right-container')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>广告名</th>
                <th>广告图片</th>
                <th>广告URL</th>
                @if($type=='retailer' || $type=='app')
                    <th>类型</th>
                    @if($type=='app')
                        <th>App</th>
                    @endif
                @endif
                <th>时长</th>
                <th>开始时间</th>
                <th>结束时间</th>
                <th class="text-nowrap">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td><img src="{{ $record->link_path }}" />pic</td>
                    <td>{{ $record->link_path }}</td>
                    @if($type=='retailer' || $type=='app')
                        <td>{{ $record->ad_type }}</td>
                        @if($type=='app')
                            <td>{{ $record->app_type }}</td>
                        @endif
                    @endif
                    <td>{{ $record->time_type }}</td>
                    <td>{{ $record->started_at}}</td>
                    <td>{{ $record->end_at}}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/advert/'. $record->id .'/edit?type='.$type) }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <button type="button" class="btn btn-danger ajax" data-method="delete"
                                    data-url="{{ url('admin/advert/'.$record->id ) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>






@stop