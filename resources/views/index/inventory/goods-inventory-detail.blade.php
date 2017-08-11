@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '商品出入库明细')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">商品出入库明细</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at']}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at']}}">
                        <button type="button" class=" btn btn-blue-lighter search control search-by-get">搜索</button>
                        <button onclick="javascript:history.back()" type="button" class="btn btn-default control">返回
                        </button>
                    </form>
                </div>
                <div class="col-sm-6 details-item">
                    <label>商品名称：</label>
                    <div class="content">{{$goods->name}}</div>
                </div>
                <div class="col-sm-6 details-item">
                    <label>商品条形码：</label>
                    <div class="content">{{$goods->bar_code}}</div>
                </div>
                <div class="col-sm-12 table-responsive table-wrap">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>生产日期</th>
                            <th>出入库单号</th>
                            <th>订单号</th>
                            {{--<th>商品名称</th>--}}
                            <th>类型</th>
                            <th>出/入库数量</th>
                            <th>出/入库人</th>
                            <th>出/入库时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($goods))
                            @foreach($lists as $inventory)
                                <tr>
                                    <td>{{$inventory->production_date ?? ''}}</td>
                                    <td>{{$inventory->inventory_number ?? ''}}</td>
                                    <td>{{$inventory->order_number > 0 ? $inventory->order_number : '---'}}</td>
                                    <td>{{cons()->valueLang('inventory.inventory_type',$inventory->inventory_type).cons()->valueLang('inventory.action_type',$inventory->action_type)}}</td>
                                    <td>{{$inventory->action_type == cons('inventory.action_type.in') ? '+' : '-'}} {{$inventory->transformation_quantity}}{{$inventory->source > 0 ? '('.cons()->valueLang('inventory.source',$inventory->source).')' : ''}}</td>
                                    <td>{{$inventory->user->user_name ?? '系统'}}</td>
                                    <td>{{$inventory->created_at}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    @if(isset($lists))
                        <div class="col-sm-12 text-right">
                            {!! $lists->appends($data ?? [])->render() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            formSubmitByGet();
        })
    </script>
@stop
