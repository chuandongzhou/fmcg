@extends('index.menu-master')
@section('subtitle', '个人中心-配送人员')

@section('right')
    @include('index.personal.tabs')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div class="text-right add">
                    <a href="{{ url('personal/delivery-man/create') }}" class="btn btn-primary">添加</a>
                </div>
                <table class="table table-bordered text-center">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>联系方式</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($deliveryMen as $man)
                        <tr>
                            <td>
                                {{ $man->name }}
                            </td>
                            <td>
                                {{ $man->phone }}
                            </td>
                            <td>

                                <div role="group" class="btn-group btn-group-xs">
                                    <a href="{{ url('personal/delivery-man/'. $man->id . '/edit') }}"
                                       class="btn btn-primary">
                                        <i class="fa fa-edit"></i> 编辑
                                    </a>
                                    <a data-url="{{ url('api/v1/personal/delivery-man/'. $man->id) }}"
                                       data-method="delete" class="btn btn-danger ajax" type="button">
                                        <i class="fa fa-trash-o"></i> 删除
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @parent
@stop
