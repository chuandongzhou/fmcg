@extends('admin.master')

@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/column/') }}" data-help-class="col-sm-push-2 col-sm-10">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>栏目名</th>
                <th>{{ cons()->valueLang('home_column.type' , $typeId) }}id</th>
                <th>排序</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($homeColumns as $homeColumn)
                <tr>
                    <td>{{ $homeColumn->name }}</td>
                    <td>{{ $homeColumn->id_list }}</td>
                    <td>{{  cons()->valueLang('sort.' . $type ,array_get(cons('sort.' . $type) ,$homeColumn->sort)) }}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="btn btn-primary" href="{{ url('admin/column/' . $homeColumn->id . '/edit') }}">
                                <i class="fa fa-edit"></i> 编辑
                            </a>
                            <a type="button" class="btn btn-danger ajax" data-method="delete"
                               data-url="{{ url('admin/column/' . $homeColumn->id) }}">
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