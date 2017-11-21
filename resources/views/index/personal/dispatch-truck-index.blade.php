@extends('index.manage-master')
@section('subtitle', '个人中心-配送管理-发车单')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/delivery-man') }}">配送管理</a> >
                    <span class="second-level"> 发车单</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form action="{{url('personal/dispatch-truck')}}" method="get" autocomplete="off">
                        <input data-format="YYYY-MM-DD" class="enter control datetimepicker" name="start_at"
                               placeholder="开始时间" type="text" value="{{array_get($data,'start_at')}}">至
                        <input data-format="YYYY-MM-DD" class="enter control datetimepicker" name="end_at"
                               placeholder="结束时间" type="text" value="{{array_get($data,'end_at')}}">
                        <select class="control" name="delivery_man">
                            <option value="">选择配送员</option>
                            @foreach($deliveryMans as $deliveryMan)
                                <option @if($deliveryMan->id == array_get($data,'delivery_man')) selected
                                        @endif value="{{$deliveryMan->id}}">{{$deliveryMan->name}}</option>
                            @endforeach
                        </select>
                        <input class="enter control" placeholder="请输入单号/车牌号" value="{{array_get($data,'number_license')}}" name="number_license" type="text">
                        <button type="button" class=" btn btn-blue-lighter search control search-by-get">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive">
                    <table class="table table-bordered table-center public-table">
                        <thead>
                        <tr>
                            <th>发车单号</th>
                            <th>车辆名称</th>
                            <th>车牌号</th>
                            <th>配送人</th>
                            <th>配送订单单数</th>
                            <th>发车时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dispatchTrucks as $dispatchTruck)
                            <tr>
                                <td>{{$dispatchTruck->id}}</td>
                                <td>{{$dispatchTruck->truck->name ?? ''}}</td>
                                <td>{{$dispatchTruck->truck->license_plate ?? ''}}</td>
                                <td>{!! implode("|",array_column($dispatchTruck->deliveryMans->toArray(), 'name')) !!}</td>
                                <td>{{$dispatchTruck->orders->count()}}</td>
                                <td>{{$dispatchTruck->dispatch_time}}</td>
                                <td>{{$dispatchTruck->status_name}}</td>
                                <td><a href="{{url('personal/dispatch-truck/detail').'/'.$dispatchTruck->id}}"><i
                                                class="iconfont color-blue icon-chakan"></i>查看</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $dispatchTrucks->appends($data)->render() !!}
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script>
        $(function () {
            formSubmitByGet();
        })
    </script>
@stop

