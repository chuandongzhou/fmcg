@extends('index.manage-master')
@section('subtitle', '业务管理-'.(check_role('maker') ? '供应商':'厂家交易').'申请')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--页面中间内容开始-->
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    @if(check_role('maker'))
                        <a href="{{ url('business/salesman-customer?type=supplier') }}">供应商管理</a> >
                    @endif
                    <span class="second-level">{{check_role('maker') ? '供应商':'厂家交易'}}申请</span>
                </div>
            </div>
            <div class="row salesman">
                <p class="col-sm-12">
                    <i class="iconfont icon-tishi orange"></i>
                    申请通过的供应商才能与对应的厂商在订百达平台进行交易
                </p>
                <div class="col-sm-12 form-group">
                    <table class="table table-bordered table-center table-middle public-table">
                        @if($user->type < cons('user.type.maker'))
                            <thead>
                            <tr>
                                <th>编号</th>
                                <th>厂家名称</th>
                                <th>平台账号</th>
                                <th>联系人</th>
                                <th>联系方式</th>
                                <th>营业地址</th>
                                <th>指派业务员</th>
                                <th>状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($applyLists as $key => $applyList)
                                <tr>
                                    <td>{{$key +1}}</td>
                                    <td>{{$applyList->name}}</td>
                                    <td>{{$applyList->account}}</td>
                                    <td>{{$applyList->contact}}</td>
                                    <td>{{$applyList->contact_mobile}}</td>
                                    <td>{{$applyList->address}}</td>
                                    <td>{{$applyList->salesman_name}}</td>
                                    <td>
                                        <b class="@if($applyList->status)color-blue @else red @endif ">{{$applyList->status_name}}</b>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        @else
                            <thead>
                            <tr>
                                <th>编号</th>
                                <th>客户名称</th>
                                <th>平台账号</th>
                                <th>联系人</th>
                                <th>联系方式</th>
                                <th>营业地址</th>
                                <th>收货地址</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($applyLists as $key => $applyList)
                                @if(!$applyList->status)
                                    <tr>
                                        <td>{{$key +1}}</td>
                                        <td>{{$applyList->name}}</td>
                                        <td>{{$applyList->account}}</td>
                                        <td>{{$applyList->contact}}</td>
                                        <td>{{$applyList->contact_mobile}}</td>
                                        <td>{{$applyList->address}}</td>
                                        <td>{{$applyList->shipping_address}}</td>
                                        <td>
                                            <a href="javascript:;" class="color-blue"
                                               data-target="#salesmanModal"
                                               data-toggle="modal" data-name="{{$applyList->name}}"
                                               data-supplier_id="{{$applyList->supplier_id}}"
                                               data-account="{{$applyList->account}}">
                                                <i class="iconfont icon-tongguo"></i>通过
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('includes.set-salesman-modal')
@stop

@section('js')
    @parent
    <script type="text/javascript">

    </script>
@stop
