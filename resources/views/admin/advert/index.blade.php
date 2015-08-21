@extends('admin.master')

@section('right-container')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>广告类型</th>
            <th>名称</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>状态</th>
            <th class="text-nowrap">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($adverts as $advert)
            <tr>
                <td>{{ cons()->valueLang('advert.type', $advert->type) }}</td>
                <td>{{ $advert->name }}</td>
                <td>{{ $advert->start_at }}</td>
                <td>{{ $advert->end_at }}</td>
                <td>{{ $advert->status_name }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <a class="btn btn-primary" href="{{ url('admin/advert-' .$type. '/' .$advert->id. '/edit') }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <button type="button" class="btn btn-danger ajax" data-method="delete"
                                data-url="{{ url('admin/advert/' . $advert->id ) }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $adverts->appends(['type' => $type])->render() !!}
@stop