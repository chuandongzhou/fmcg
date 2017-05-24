@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '出库明细')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">出库明细</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <a type="button" class="btn btn-default control" href="javascript:history.back()">返回</a>
                </div>
                <div class="col-sm-12 outh-details-wrap">
                    <ul>
                        <li>
                            <label>出库单号：</label>
                            <div class="content prompt">{{$inventory['0']->inventory_number ?? ''}}</div>
                        </li>
                        @if($inventory['0']->inventory_type == cons('inventory.inventory_type.system'))
                            <li>
                                <label>售货单号：</label>
                                <div class="content prompt">{{$inventory['0']->order_number > 0 ? $inventory['0']->order_number : '---'}}</div>
                            </li>
                            <li>
                                <label>买家名称：</label>
                                <div class="content prompt">{{$inventory['0']->buyer_name ?? '---'}}</div>
                            </li>
                        @endif
                        <li>
                            <label>总计盈利： </label>
                            <div class="content prompt"> @if($total_profit > 0)+@endif {{$total_profit}}</div>
                        </li>
                        <li>
                            <label>备注 </label>
                            <a class="iconfont icon-tixing" title=""
                               data-container="body" data-toggle="popover" data-placement="bottom"
                               data-content="{{$inventory['0']->remark ?? '无'}}">
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>商品条形码</th>
                            <th>生产日期</th>
                            <th>出库数量</th>
                            <th>成本单价</th>
                            <th>出库单价</th>
                            <th>盈利（元）</th>
                            <th>类型</th>
                            <th>出库人</th>
                            <th>出库时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inventory as $item)
                            <tr>
                                <td>
                                    <div class="product-name"
                                         title="{{$item->goods->name}}">{{$item->goods->name}}</div>
                                </td>
                                <td>{{$item->goods->bar_code}}</td>
                                <td>{{$item->production_date ?? ''}}</td>
                                <td>{{$item->transformation_quantity}}</td>
                                <td>{{$item->in_cost}} 元/{{cons()->valueLang('goods.pieces',$item->in_pieces)}}</td>
                                <td>{{$item->cost}} 元/{{cons()->valueLang('goods.pieces',$item->pieces)}}</td>
                                <td> @if($item->profit > 0) + {{$item->profit}}@else <span
                                            class="red">{{$item->profit}}</span> @endif</td>
                                <td>{{cons()->valueLang('inventory.inventory_type',$item->inventory_type).cons()->valueLang('inventory.action_type',$item->action_type)}}</td>
                                <td>{{$item->user->user_name ?? '系统'}}</td>
                                <td>{{$item->created_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    @if(isset($inventory))
                        {!! $inventory->appends($data ?? '')->render() !!}
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
            $("[data-toggle='popover']").popover();
        })
    </script>
@stop
