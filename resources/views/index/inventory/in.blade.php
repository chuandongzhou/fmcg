@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '入库')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">入库</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at']}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at']}}">
                        <input class="enter control" placeholder="商品名称/条形码" name="goods" type="text"
                               value="{{$data['goods'] ?? ''}}">
                        <button type="button" class=" btn btn-blue-lighter search control search-by-get">搜索</button>
                        <a href="{{ url('inventory/in-create') }}" class="btn btn-default control">我要入库</a>
                        <a href="{{ url('inventory/in-export') . '?start_at='.($data['start_at'] ?? '') . '&end_at=' .($data['end_at'] ?? '') . '&goods='.($data['goods'] ?? '') }}"
                           class="btn btn-default control">导出</a>
                        <div class="warehousing-error-btn red">入库异常<a href="{{ url('inventory/in-error') }}"
                                                                      class="badge badge-danger">{{$errorCount ?? 0}}</a>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive table-wrap">
                    <table class="public-table MyTable1 table-scroll">
                        <thead>
                        <tr>
                            <td>商品名称</td>
                            <td>商品条形码</td>
                            <td>生产日期</td>
                            <td>入库数量</td>
                            <td>入库单价</td>
                            <td>类型</td>
                            <td>入库人</td>
                            <td>进货单号</td>
                            <td>供货商家</td>
                            <td>入库时间</td>
                            <td>备注</td>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($lists))
                            @foreach($lists as $list)
                                <tr>
                                    <td>{{$list->goods_name ?? ''}}
                                        {{$list->source > 0 ? '('.cons()->valueLang('inventory.source',$list->source).')' : ''}}
                                    </td>
                                    <td>{{$list->goods_barcode}}</td>
                                    <td>{{$list->production_date ?? '---'}}</td>
                                    <td>{{$list->transformation_quantity?? ''}}</td>
                                    <td>{{$list->cost ?? '0'}}
                                        / {{cons()->valueLang('goods.pieces',$list->pieces)}}</td>
                                    <td>{{cons()->valueLang('inventory.inventory_type',$list->inventory_type).cons()->valueLang('inventory.action_type',$list->action_type)}}</td>
                                    <td>{{$list->operator_name}}</td>
                                    <td>{{$list->order_number > 0 ?$list->order_number: '---'}}</td>
                                    <td>{{$list->seller_name or '---'}}</td>
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
            var tableWidth = $(".table-scroll").parents("div").width();
            FixTable("MyTable1", 1, tableWidth, 900);
        })
    </script>
@stop
