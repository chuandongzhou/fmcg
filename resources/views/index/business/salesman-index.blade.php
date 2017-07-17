@extends('index.manage-master')
@section('subtitle', '业务管理-业务员管理')
@include('includes.salesman')
@include('includes.cropper')
@include('includes.renew')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level"> 业务员管理</span>
                </div>
            </div>
            <form action="{{ url('business/salesman') }}" method="get">
                <div class="row salesman">
                    <div class="col-sm-12 form-group salesman-controls">
                        <a class="btn btn-blue-lighter " href="#" type="button" data-target="#salesmanModal"
                           data-toggle="modal" data-id="0">
                            <i class="fa fa-plus"></i>添加业务员
                        </a>
                        <span class="item control-item">
                        <input class="control" type="text" name="name" value="{{ $name }}" placeholder="账号、名称">
                        <button type="submit" class="btn btn-blue-lighter search-by-get">查询</button>
                    </span>
                    </div>
                    <div class="col-sm-12 form-group">
                        <table class="table table-bordered table-center table-middle public-table">
                            <thead>
                            <tr>
                                <th>选择</th>
                                <th>名称</th>
                                <th>账号</th>
                                <th>联系方式</th>
                                <th>过期时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($salesmen as $man)
                                <tr>
                                    <td class="check">
                                        <input type="checkbox" class="child" name="salesman_id[]"
                                               value="{{ $man->id }}">
                                    </td>
                                    <td class="clerk-name">
                                        <img class="avatar" src="{{ $man->avatar_url }}"> {{ $man->name }}
                                    </td>
                                    <td>
                                        {{ $man->account }}
                                    </td>
                                    <td>
                                        {{ $man->contact_information }}
                                    </td>
                                    <td>
                                        {{ $man->expire  }}
                                    </td>
                                    <td>
                                        <div role="group" class="btn-group btn-group-xs">
                                            <a class="edit" href="javascript:void(0)" type="button"
                                               data-target="#salesmanModal" data-toggle="modal"
                                               data-id="{{ $man->id }}">
                                                <i class="iconfont icon-xiugai"></i> 编辑
                                            </a>
                                            <a class="color-blue" href="{{ url('business/report/' . $man->id) }}">
                                                <i class="iconfont icon-jingzhibaogao"></i> 报告&nbsp;</a>
                                            @if($man->status==cons('status.off'))
                                                <a class="ajax gray-light"
                                                   data-url="{{ url('api/v1/business/salesman/lock') }}"
                                                   data-data='{ "id": "{{ $man->id }}","status":"{{ $man->status }}" }'
                                                   data-method="post" type="button">
                                                    <i class="iconfont icon-dongjietubiao"></i> 解冻&nbsp;
                                                </a>
                                            @else
                                                <a class="black ajax"
                                                   data-url="{{ url('api/v1/business/salesman/lock') }}"
                                                   data-data='{ "id": "{{ $man->id }}","status":"{{ $man->status }}" }'
                                                   data-method="post" type="button">
                                                    <i class="iconfont icon-dongjietubiao"></i> 冻结&nbsp;
                                                </a>
                                            @endif
                                            @if($man->expire_at)
                                                <a data-target="#expireModal" data-toggle="modal" data-type="salesman"
                                                   data-id="{{ $man->id }}">
                                                    <i class="iconfont icon-chaopiao"></i>续费</a>
                                            @endif
                                            @if(auth()->user()->type != cons('user.type.maker'))
                                                <a data-url="{{ url('api/v1/business/salesman/'. $man->id) }}"
                                                   data-method="delete"
                                                   class="red ajax" type="button"><i class="iconfont icon-shanchu"></i>
                                                    删除
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="col-sm-12 form-group remove-panel">
                        <label><input type="checkbox" class="parent"> 全选</label>
                        <a data-url="{{ url('api/v1/business/salesman/batch-delete') }}" data-method="delete"
                           class="btn btn-red ajax" type="button">
                            批量删除
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
    </script>
@stop
