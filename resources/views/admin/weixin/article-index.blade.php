@extends('admin.master')

@section('right-container')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>序号</th>
            <th>名称</th>
            <th>链接地址</th>
            <th class="text-nowrap">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($articles as $article)
            <tr>
                <td>{{ $article-> id}}</td>
                <td>{{ $article->title }}</td>
                <td>{{ $article->link_url }}</td>
                <td>
                    <div class="btn-group btn-group-xs" role="group">
                        <a class="btn btn-primary" href="{{ url('admin/weixin/article/' . $article->id . '/edit') }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <button type="button" class="btn btn-danger ajax" data-method="delete"
                                data-url="{{ url('admin/weixin/article/' . $article->id ) }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop