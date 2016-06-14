@extends('index.menu-master')
@section('subtitle', '业务管理-业务员管理')
@include('includes.salesman')
@include('includes.cropper')

@section('right')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <a class="add" href="#" type="button"
                       data-target="#salesmanModal"
                       data-toggle="modal" data-id="0"><i class="fa fa-plus"></i> 添加业务员
                    </a>

                </div>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>账号</th>
                        <th>联系方式</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salesmen as $man)
                        <tr>
                            <td>
                                {{ $man->name }}
                            </td>
                            <td>
                                {{ $man->account }}
                            </td>
                            <td>
                                {{ $man->contact_information }}
                            </td>
                            <td>

                                <div role="group" class="btn-group btn-group-xs">
                                    <a class="btn btn-primary" href="javascript:void(0)" type="button"
                                       data-target="#salesmanModal"
                                       data-toggle="modal" data-id="{{ $man->id }}"><i class="fa fa-edit"></i> 编辑
                                    </a>

                                    <a data-url="{{ url('api/v1/business/salesman/'. $man->id) }}"
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
