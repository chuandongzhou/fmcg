@extends('index.menu-master')
@section('subtitle', '业务管理-业务员管理')
@section('top-title')
    <a href="{{ url('business/salesman') }}">业务管理</a> &rarr;
    业务员管理
@stop
@include('includes.salesman')
@include('includes.cropper')

@section('right')
    <form action="{{ url('business/salesman') }}" method="get">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div class="col-sm-12 form-group">
                    <span class="item control-item">
                       <input class="inline-control" type="text" name="name" value="{{ $name }}" placeholder="账号、名称">
                    </span>
                    <span class="item control-item">
                        <button type="submit" class="btn btn-default search-by-get">查询</button>
                        <a class="btn btn-default" href="#" type="button" data-target="#salesmanModal"
                           data-toggle="modal" data-id="0">
                            <i class="fa fa-plus"></i>添加业务员
                        </a>
                    </span>
                </div>

                <div class="col-sm-12 form-group">
                    <table class="table table-bordered table-center table-middle">
                        <thead>
                        <tr>
                            <th></th>
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
                                    <input type="checkbox" class="child" name="salesman_id[]" value="{{ $man->id }}">
                                </td>
                                <td class="clerk-name">
                                    <img class="avatar" src="{{ $man->avatar_url }}">  {{ $man->name }}
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

                                        <a class="btn btn-default"
                                           href="{{ url('business/report/' . $man->id) }}"><i
                                                    class="fa fa-book"></i> 报告</a>

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

                <div class="col-sm-12 form-group">
                    <span class="item control-item">
                       <input type="checkbox" class="parent"> 全选
                    </span>
                    <span class="item control-item">
                        <a data-url="{{ url('api/v1/business/salesman/batch-delete') }}" data-method="delete"
                           class="btn btn-danger ajax" type="button">
                            <i class="fa fa-trash-o"></i> 批量删除
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </form>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
    </script>
@stop
