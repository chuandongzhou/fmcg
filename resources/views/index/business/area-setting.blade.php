@extends('index.manage-master')
@section('subtitle', '业务管理-业务区域')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--页面中间内容开始-->
            <div class="row">
                <div class="col-xs-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level">配送区域</span>
                </div>
            </div>
            <div class="row margin-clear">
                <div class="col-sm-12 table-responsive">
                    <div class="delivery-area-wrap">
                        <a class="personal-add" data-target="#area" data-toggle="modal"><label><span
                                        class="fa fa-plus"></span></label> 业务区域
                        </a>
                        <table class="table table-bordered table-center">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>业务区域</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($areas as $area)
                                <tr>
                                    <td>
                                        {{$area->id}}
                                    </td>
                                    <td>
                                        {{$area->name}}
                                    </td>
                                    <td>
                                        {{$area->remark}}
                                    </td>
                                    <td>
                                        <div role="group" class="btn-group btn-group-xs">
                                            <a href="javascript:;"
                                               class="edit" data-target="#area" data-toggle="modal"
                                               data-id="{{$area->id}}"
                                               data-name="{{$area->name}}"
                                               data-remark="{{$area->remark}}">
                                                <i class="iconfont icon-xiugai"></i> 修改
                                            </a>
                                            <a href="javascript:;" data-method="delete"
                                               data-url="{{ url('api/v1/business/area/' . $area->id) }}"
                                               class="red no-form ajax" type="button">
                                                <i class="iconfont icon-shanchu2"></i> 删除
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('includes.area-setting-modal')
@stop
@section('js')
    @parent
@stop
