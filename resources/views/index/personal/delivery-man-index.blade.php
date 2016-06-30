@extends('index.menu-master')
@section('subtitle', '个人中心-配送人员')
@include('includes.delivery-man')
@section('top-title', '个人中心-配送人员')
@section('right')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <a class="add" href="javascript:void(0)" type="button" data-target="#deliveryModal"
                       data-toggle="modal"><label><span class="fa fa-plus"></span></label>添加配送人员
                    </a>
                </div>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>联系方式</th>
                        <th>POS机登录名</th>
                        <th>POS机编号</th>
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
                                {{ $man->user_name }}
                            </td>
                            <td>
                                {{ $man->pos_sign }}
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
