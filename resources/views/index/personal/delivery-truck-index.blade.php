@extends('index.manage-master')
@section('subtitle', '个人中心-配送车辆')
@include('includes.delivery-truck')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/delivery-man') }}">配送管理</a> >
                    <span class="second-level"> 配送车辆</span>
                </div>
            </div>
            <div class="row coupon">
                <div class="col-sm-12 table-responsive">
                    <div class="add-coupon">
                        <a class="add btn btn-blue-lighter update-modal" href="javascript:" data-toggle="modal"
                           data-target="#deliveryTruckModal">
                            <label>
                                <span class="fa fa-plus"></span>
                            </label>添加配送车辆
                        </a>
                    </div>
                    <table class="table table-bordered table-center public-table">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>车辆名称</th>
                            <th>车牌号</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($deliveryTrucks as $truck)
                            <tr>
                                <td>
                                    {{ $truck->id }}
                                </td>
                                <td>
                                    {{ $truck->name }}
                                </td>
                                <td>
                                    {{ $truck->license_plate }}
                                </td>
                                <td>
                                    <span class="status-name">{{ cons()->valueLang('truck.status' ,$truck->status) }}</span>
                                </td>
                                <td>
                                    <div role="group" class="btn-group btn-group-xs">
                                        <a data-toggle="modal"
                                           data-target="#deliveryTruckModal" data-id="{{ $truck->id }}"
                                           data-name="{{ $truck->name }}" data-license="{{ $truck->license_plate }}"
                                           class="edit update-modal">
                                            <i class="iconfont icon-xiugai"></i> 编辑
                                        </a>
                                        <a href="javascript:" data-method="put"
                                           data-url="{{ url('api/v1/personal/delivery-truck/status/' . $truck->id)}}"
                                           data-status="{{ $truck->status }}"
                                           data-on='<i class="iconfont icon-qiyong"></i> 启用'
                                           data-off='<i class="iconfont icon-jinyong"></i> 禁用'
                                           data-change-status="true"
                                           class="ajax-no-form color-blue">
                                            {!!  $truck->status ? '<i class="iconfont icon-jinyong"></i> 禁用' : '<i class="iconfont icon-qiyong"></i> 启用' !!}
                                        </a>

                                        <a data-url="{{ url('api/v1/personal/delivery-truck/'. $truck->id) }}"
                                           data-method="delete" class="red delete-no-form ajax" href="javascript:"
                                           data-danger="真的要删除 {{ $truck->name }} 吗？"
                                           type="button">
                                            <i class="iconfont icon-shanchu"></i> 删除
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
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        ajaxNoForm();
    </script>
@stop

