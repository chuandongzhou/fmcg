@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '入库明细')
@section('top-title')
    <a href="{{ url('inventory') }}">库存管理</a> >
    <span class="second-level">入库明细</span>
@stop
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 control-search">
            <a type="button" class="btn btn-default control" href="javascript:history.back()">返回</a>
        </div>
        <div class="col-sm-3 details-item wareh-details">
            <label>入库单号：</label>
            <div class="content">{{$inventory['0']->inventory_number ?? ''}}</div>
        </div>
        <div class="col-sm-3 details-item wareh-details">
            <label>进货单号：</label>
            <div class="content">{{$inventory['0']->order_number > 0 ? $inventory['0']->order_number : '---'}}</div>
        </div>
        <div class="col-sm-3 details-item wareh-details">
            <label>卖家名称：</label>
            <div class="content">{{$inventory['0']->buyer_name ?? '---'}}</div>
        </div>
        <div class="col-sm-12 table-responsive wareh-details-table">
            <table class="table-bordered table table-center public-table">
                <thead>
                <tr>
                    <th>商品名称</th>
                    <th>商品条形码</th>
                    <th>生产日期</th>
                    <th>入库数量</th>
                    <th>入库单价</th>
                    <th>类型</th>
                    <th>入库人</th>
                    <th>出/入库时间</th>
                    <th>备注</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($inventory))
                    @foreach($inventory as $list)
                        <tr>
                            <td>
                                <div class="product-name" title="{{$list->goods->name}}">
                                    {{$list->goods->name}}
                                </div>
                            </td>
                            <td>{{$list->goods->bar_code}}</td>
                            <td>{{$list->production_date}}</td>
                            <td>{{$list->transformation_quantity}}</td>
                            <td>{{$list->cost}} 元/{{cons()->valueLang('goods.pieces',$list->pieces)}}</td>
                            <td>{{cons()->valueLang('inventory.inventory_type',$list->inventory_type).cons()->valueLang('inventory.action_type',$list->action_type)}}</td>
                            <td>{{$list->user->user_name}}</td>
                            <td>{{$list->created_at}}</td>
                            <td>
                                <a class="iconfont icon-tixing" title=""
                                   data-container="body" data-toggle="popover" data-placement="bottom"
                                   data-content="{{$list->remark ?? '无'}}">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="col-sm-12 text-right">
            @if(isset($inventory))
                {!! $inventory->appends($data ?? '')->render() !!}
            @endif
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
            $(function () {
                $("[data-toggle='popover']").popover();
            })
    </script>
@stop
