@extends('index.manage-master')
@section('subtitle', '在途商品详情')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">在途商品详情</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <a type="button" class="btn btn-default control" href="javascript:history.back()">返回</a>
                </div>
                <div class="col-sm-5 details-item">
                    <label>商品名称：</label>
                    <div class="content">{{$goodsOutDetail[0]->goods->name ??  '---'}}</div>
                </div>
                <div class="col-sm-4 details-item wareh-details">
                    <label>商品条形码：</label>
                    <div class="content">{{$goodsOutDetail[0]->goods->bar_code ??  '---'}}</div>
                </div>
                <div class="col-sm-3 details-item wareh-details">
                    <label>在途总数：</label>
                    <div class="content red">{{$inTransitTotal ?? 0}}</div>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>生产日期</th>
                            <th>数量</th>
                            <th>售货单价（元）</th>
                            <th>售货单号</th>
                            <th>发货时间</th>
                            <th>送货人</th>
                            <th>买家名称</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($goodsOutDetail))
                            @foreach($goodsOutDetail as $goods)
                                <tr>
                                    <td>{{$goods->production_date}}</td>
                                    <td>{{$goods->transformation_quantity}}</td>
                                    <td>{{$goods->cost}}</td>
                                    <td>{{$goods->order_number ?? '---'}}</td>
                                    <td>{{$goods->order->send_at}}</td>
                                    <td>{{$goods->delivery_name}}</td>
                                    <td>{{$goods->buyer_name}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $goodsOutDetail->render() !!}
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
