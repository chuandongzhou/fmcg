@extends('master')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@stop

@section('body')
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
    <div class="container">
        <div class="row order-report report-detail margin-clear">
            <div class="col-sm-12 content">
                <div class="row">
                    <div class="col-sm-12 tables">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td>业务员</td>
                                <td>客户名称</td>
                                <td>时间</td>
                                <td>操作</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{$salesman->name}}</td>
                                <td>{{$customer->name}}</td>
                                <td> {{$startDate}}至{{$endDate}}</td>
                                <td>
                                    <a href="{{ url("business/report/{$salesman->id}/customer-detail/export?start_date={$startDate}&end_date={$endDate}&customer_id={$customer->id}")}}"
                                       class="btn btn-border-blue"><i class="iconfont icon-xiazai"></i>下载打印</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-12 padding-clear">
                        <ul id="myTab" class="nav nav-tabs notice-bar padding-clear">
                            <li class="active"><a href="#table1" data-toggle="tab">拜访记录</a></li>
                            <li><a href="#table2" data-toggle="tab">销售总计</a></li>
                            <li><a href="#table3" data-toggle="tab">陈列费总计</a></li>
                            <li><a href="#table4" data-toggle="tab">赠品总计</a></li>
                            <li><a href="#table5" data-toggle="tab">促销活动总计</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content ">
                            <div class="tab-pane fade active in padding-clear tables" id="table1">
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
                            <div class="tab-pane fade padding-clear tables" id="table2">
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
                            <div class="tab-pane fade padding-clear tables" id="table3">
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
                            <div class="tab-pane fade padding-clear tables" id="table4">
                                <table class="table table-bordered table-displays">
                                    <thead>
                                    <tr>
                                        <td>拜访时间</td>
                                        <td>商品名称</td>
                                        <td>商品数量</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($gifts as $gift)
                                        <tr>
                                            <td>{{ $gift['time'] }}</td>
                                            <td>{{ $gift['goods_name'] }}</td>
                                            <td>{{ $gift['num']}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade padding-clear tables" id="table5">
                                @foreach($promos as $promo)
                                    <div class="panel-container bordered-bottom">
                                        <div class="row">
                                            <p class="col-sm-12 item-text other">
                                                促销名称 : <span class="prompt">{{$promo->name}}</span>
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 item-text other">
                                                拜访时间 : <span class="prompt">{{$promo->time}}</span>
                                            </div>
                                        </div>
                                        @include('includes.promo-content-view',['promo' => $promo])
                                        <div class="row">
                                            <div class="col-sm-12 item-text other">

                                                促销备注 : <span class="prompt">{{$promo->remark}}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('includes.templet-model')
    </body>
@stop


