@extends('index.menu-master')


@section('subtitle', '个人中心-首页广告')

@section('right')
    <div>
        <a class="add" href="{{ url('personal/model/create') }}"  ><label><span class="fa fa-plus"></span></label>添加广告
        </a>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
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
                <td>{{ $advert->name }}</td>
                <td>{{ $advert->start_at }}</td>
                <td>{{ $advert->end_at }}</td>
                <td>{{ $advert->status_name }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <a class="btn btn-primary" href="{{ url('personal/model/advert-edit/' . $advert->id) }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <button type="button" class="btn btn-danger ajax" data-method="delete"
                                data-url="{{ url('api/v1/personal/model/advert/'. $advert->id)  }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @parent
@stop
