@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '出入库明细')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">出入库明细</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form action="{{url('inventory/detail-list')}}" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at']}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at']}}">
                        <input class="enter control" placeholder="商品名称/商品条形码" name="goods" type="text"
                               value="{{$data['goods'] ?? ''}}">
                        <select onchange="typeChange(this)" class="control">
                            <option>请选择类型</option>
                            @if(isset($data['inventory_type']) && $data['inventory_type'] == 1)
                                @if($data['action_type'] == 2)
                                    <option selected value="1">系统出库</option>
                                    <option value="2">系统入库</option>
                                @else
                                    <option value="1">系统出库</option>
                                    <option selected value="2">系统入库</option>
                                @endif
                            @else
                                <option value="1">系统出库</option>
                                <option value="2">系统入库</option>
                            @endif
                            @if(isset($data['inventory_type']) && $data['inventory_type'] == 2)
                                @if($data['action_type'] == 2)
                                    <option selected value="3">手动出库</option>
                                    <option value="4">手动入库</option>
                                @else
                                    <option value="3">手动出库</option>
                                    <option selected value="4">手动入库</option>
                                @endif
                            @else
                                <option value="3">手动出库</option>
                                <option value="4">手动入库</option>
                            @endif
                        </select>
                        <input type="hidden" name="inventory_type" value="{{$data['inventory_type']}}">
                        <input type="hidden" name="action_type" value="{{$data['action_type']}}">
                        <button type="button" class=" btn btn-blue-lighter search control search-by-get">搜索</button>
                        <a href="{{ url('inventory/detail-list-export') . '?start_at='.($data['start_at'] ?? '') . '&end_at=' .($data['end_at'] ?? '') . '&goods='.($data['goods'] ?? '') }}"
                           class="btn btn-default control">导出</a>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive table-wrap">
                    <table class="public-table MyTable1 table-scroll">
                        <thead>
                        <tr>
                            <td>商品名称</td>
                            <td>商品条形码</td>
                            <td>生产日期</td>
                            <td>类型</td>
                            <td>入库数量</td>
                            <td>出库数量</td>
                            <td>成本单价</td>
                            <td>出库单价</td>
                            <td>出入库人</td>
                            <td>进出货单号</td>
                            <td>供进货商家</td>
                            <td>出入库时间</td>
                            <td>备注</td>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists))
                            @foreach($lists as $list)
                                <tr>
                                    <td>{{$list->goods_name ?? ''}}</td>
                                    <td>{{$list->goods_barcode}}</td>
                                    <td>{{$list->production_date ?? '---'}}</td>
                                    <td>{{cons()->valueLang('inventory.inventory_type',$list->inventory_type).cons()->valueLang('inventory.action_type',$list->action_type)}}</td>

                                    <td>{{$list->action_type == cons('inventory.action_type.in') ? $list->transformation_quantity : '--'}}</td>
                                    <td>{{$list->action_type == cons('inventory.action_type.out') ? $list->transformation_quantity : '--'}}</td>
                                    <td>
                                        @if($list->action_type == cons('inventory.action_type.out'))
                                            {{$list->in_cost . '/' .cons()->valueLang('goods.pieces',$list->in_pieces)}}
                                        @else
                                            {{$list->cost . '/' .cons()->valueLang('goods.pieces',$list->pieces)}}
                                        @endif
                                    </td>
                                    <td>{{$list->action_type == cons('inventory.action_type.out') ? $list->cost . '/' .cons()->valueLang('goods.pieces',$list->pieces) : '---'}}</td>
                                    <td>{{$list->user->user_name ?? '系统'}}</td>
                                    <td>{{$list->order_number > 0 ? $list->order_number:'---'}}</td>
                                    <td>{{$list->action_type == cons('inventory.action_type.out') ? ($list->buyer_name ?? '---') : ($list->saller_name ?? '---')}}</td>
                                    <td>{{$list->created_at}}</td>
                                    <td>{{$list->remark}}</td>
                                </tr>
                            @endforeach

                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $lists->appends($data)->render() !!}
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
            typeChange = function (obj) {
                var type = $(obj).find('option:selected').val(),
                        inventory_type,
                        action_type;
                switch (type) {
                    case '1':
                        inventory_type = 1;
                        action_type = 2;
                        break;
                    case '2' :
                        inventory_type = 1;
                        action_type = 1;
                        break;
                    case '3' :
                        inventory_type = 2;
                        action_type = 2;
                        break;
                    case '4' :
                        inventory_type = 2;
                        action_type = 1;
                        break;
                }
                $('input[name = inventory_type]').val(inventory_type);
                $('input[name = action_type]').val(action_type)
            }

            $(function () {
                var tableWidth = $(".table-scroll").parents("div").width();
                FixTable("MyTable1", 1, tableWidth, 900);
            });
        })
    </script>
@stop
