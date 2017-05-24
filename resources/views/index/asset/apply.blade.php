@extends('index.manage-master')
@section('subtitle', '资产管理')
@include('includes.asset-modal')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/unused') }}">资产管理</a> >
                    <span class="second-level">资产申请审核</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at'] ?? ''}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at'] ?? ''}}">
                        <input class="enter control" placeholder="资产编号/资产名称" value="{{$data['asset'] ?? ''}}"
                               name="asset"
                               type="text">
                        <select name="salesmen" class="control">
                            <option value="">请选择业务员</option>
                            @foreach($salesmens as $salesmen)
                                <option @if($data['salesmen'] == $salesmen->id) selected
                                        @endif value="{{$salesmen->id}}">{{$salesmen->name}}</option>
                            @endforeach
                        </select>
                        <select name="status" class="control">
                            <option selected value="">请选择审核状态</option>
                            @foreach(cons('asset_apply.status') as $status)
                                <option @if($data['status'] == $status && !is_null($data['status'])) selected
                                        @endif value="{{$status}}">{{cons()->valueLang('asset_apply.status',$status)}}</option>
                            @endforeach
                        </select>
                        <button type="button" class=" btn btn-blue-lighter search-by-get control ">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>资产编号</th>
                            <th>资产名称</th>
                            <th>申请时间</th>
                            <th>客户名称</th>
                            <th>审核状态</th>
                            <th>申请人（业务员）</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($assetApply))
                            @foreach($assetApply as $apply)
                                <tr>
                                    <td>{{$apply->asset->id ?? ''}}</td>
                                    <td>{{$apply->asset->name ?? ''}}</td>
                                    <td>{{$apply->created_at}}</td>
                                    <td>{{$apply->client->name ?? ''}}</td>
                                    <td class="@if($apply->status == cons('asset_apply.status.approved'))blue @endif">{{cons()->valueLang('asset_apply.status',$apply->status)}}</td>
                                    <td>{{$apply->salesman->name ?? ''}}</td>
                                    <td>
                                        <a class="color-blue" href="{{url('asset/apply-detail/'.$apply->id)}}">
                                            <i class="iconfont icon-chakan"></i>查看</a>
                                        @if($apply->status == cons('asset_apply.status.not_audit'))
                                            <a data-url="{{ url('api/v1/asset/apply/review/'.$apply->id) }}"
                                               data-method="put"
                                               data-data='{"status" : "{{cons('asset_apply.status.approved')}}"}'
                                               class="ajax">
                                                <i class="iconfont  icon-tongguo"></i>通过
                                            </a>
                                            <a data-url="{{ url('api/v1/asset/apply/delete/'.$apply->id) }}"
                                               data-method="put"
                                               data-data='{"status" : "{{cons('asset_apply.status.delete')}}"}'
                                               class="red ajax">
                                                <i class="iconfont icon-shanchu"></i> 删除
                                            </a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    @if(isset($assetApply))
                        {!! $assetApply->render() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop
