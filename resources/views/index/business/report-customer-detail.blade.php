@extends('master')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@stop

@section('body')
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
    <div class="container">
        <div class="row order-report report-detail margin-clear">
            <div class="col-sm-12 content">

                <a href="{{ url("business/report/{$salesmanId}/customer-detail/export?start_date={$startDate}&end_date={$endDate}&customer_id={$customerId}")}}"
                   class="btn btn-border-blue"><i class="iconfont icon-xiazai"></i>下载打印</a>
                <div class="col-sm-12 tables">
                    <p class="title-table">拜访记录</p>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>拜访时间</td>
                            <td>提交地址</td>
                            <td>订货金额</td>
                            <td>退货金额</td>
                            <td>陈列费</td>
                            <td>拜访照片</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($visitLists as $visit)
                            <tr>
                                <td>{{ $visit['time'] }}</td>
                                <td>{{ $visit['commitAddress'] }}</td>
                                <td>{{ $visit['orderAmount'] }}</td>
                                <td>{{ $visit['returnAmount'] }}</td>
                                <td>{{ $visit['hasDisplay'] }}</td>
                                <td>
                                    @foreach($visit['photos'] as $photo)
                                        <a class="templet-modal" href="javascript:;" data-src="{{ $photo }}"
                                           data-target="#templetModal" data-toggle="modal"><img width="50px"
                                                                                                height="50px"
                                                                                                src="{{$photo}}"
                                                                                                alt="拜访照片"></a>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12  tables">
                    <p class="title-table">销售总计</p>
                    <table class="table table-scroll table-bordered table-goods-statistics">
                        <thead>
                        <tr>
                            <td>商品ID</td>
                            <td>商品名称</td>
                            <td>库存</td>
                            <td>生产日期</td>
                            <td>退货数量</td>
                            <td>退货金额</td>
                            <td>订货总数量</td>
                            <td>订货总金额</td>
                            <td>平均单价</td>
                            <td>订货数量</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($salesGoods as $item)
                            @foreach($item['pieces'] as $piece=> $value)
                                @if($piece ==array_keys($item['pieces'])[0])
                                    <tr>
                                        <td rowspan="{{ $rowspan=count($item['pieces'])}}">{{ $item['id'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['name'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['stock'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['productionDate'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['returnCount'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['returnAmount'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['count'] }}</td>
                                        <td rowspan="{{ $rowspan  }}">{{ $item['amount'] }}</td>
                                        @endif
                                        <td>{{ ( $value['num'] ? number_format(bcdiv($value['amount'], $value['num'], 2), 2) : 0) . '/' . cons()->valueLang('goods.pieces', $piece) }}</td>
                                        <td>{{ $value['num'] }}</td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12  tables">
                    <p class="title-table">陈列费</p>
                    <table class="table table-bordered table-displays">
                        <thead>
                        <tr>
                            <td>拜访时间</td>
                            <td>月份</td>
                            <td>名称</td>
                            <td>数量/金额</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($displays as $item)
                            <tr>
                                <td>{{ $item['time'] }}</td>
                                <td>{{ $item['month'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['used'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('includes.templet-model')
    </body>
@stop


