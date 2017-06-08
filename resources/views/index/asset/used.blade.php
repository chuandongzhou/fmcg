@extends('index.manage-master')
@section('subtitle', '资产管理')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/unused') }}">资产管理</a> >
                    <span class="second-level">已使用</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="use_start_at" placeholder="开始时间" type="text"
                               value="{{$data['use_start_at'] ?? ''}}">至
                        <input class="enter control datetimepicker" name="use_end_at" placeholder="结束时间" type="text"
                               value="{{$data['use_end_at'] ?? ''}}">
                        <select name="name" class="control">
                            <option value=" ">请选择资产名称型号</option>
                            @foreach($assets as $asset)
                                <option @if($data['name'] == $asset->name ) selected
                                        @endif value="{{$asset->name}}">{{$asset->name}}</option>
                            @endforeach
                        </select>
                        <input name="condition" class="enter control" placeholder="客户名称/业务员/资产编号" type="text"
                               value="{{$data['condition'] ?? ''}}">
                        <button type="button" class=" btn btn-blue-lighter search-by-get control ">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>资产编号</th>
                            <th>资产名称</th>
                            <th>开始使用时间</th>
                            <th>客户名称</th>
                            <th>营业地址</th>
                            <th>业务员</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($used))
                            @foreach($used as $use)
                                <tr>
                                    <td>{{$use->asset->id}}</td>
                                    <td>{{$use->asset->name}}</td>
                                    <td>{{$use->use_date}}</td>
                                    <td>{{$use->client->name}}</td>
                                    <td>{{$use->client->shopAddress->area_name ?? ''}}</td>
                                    <td>{{$use->salesman->name ?? ''}}</td>
                                    <td>
                                        <a class="color-blue"
                                           data-target="#view"
                                           data-toggle="modal"
                                           data-asset_name="{{$use->asset->name}}"
                                           data-asset_condition="{{$use->asset->condition}}"
                                           data-asset_remark="{{$use->asset->remark}}"
                                           data-asset_created_at="{{$use->asset->created_at}}"
                                           data-client_name="{{$use->client->name}}"
                                           data-client_contact_person="{{$use->client->contact_person}}"
                                           data-client_contact_info="{{$use->client->contact_info}}"
                                           data-client_shopAddress_address_name="{{$use->client->shopAddress->address_name}}"
                                           data-use_date="{{$use->use_date}}"
                                           data-salesman_name="{{$use->salesman->name}}"
                                           data-created_at="{{$use->created_at}}"
                                           data-pass_date="{{$use->pass_date}}"
                                           data-apply_remark="{{$use->apply_remark}}"
                                        >
                                            <i class="iconfont icon-chakan"></i>查看
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $used->render()  ?? ''!!}
                </div>
            </div>
        </div>
    </div>
    @include('includes.asset-modal')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop
