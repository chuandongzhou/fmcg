@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/shop-column') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>栏目名</th>
                <th>排序</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shopColumns as $shopColumn)
                <tr>
                    <td>{{ $shopColumn->name }}</td>
                    <td>{{ implode('|' ,$shopColumn->id_list) }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/shop-column/' . $shopColumn->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/shop-column/' . $shopColumn->id) }}">
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