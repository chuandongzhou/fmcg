@extends('index.menu-master')
@section('subtitle', '个人中心-店铺介绍')
@include('includes.qrcode')
@include('includes.notice')
@section('right')
    <div class="col-sm-12 col-xs-9">
        <div class="row store personal-store">
            <div class="col-sm-5">
                <div class="store-panel">
                    <img class="avatar" src="{{ $shop->logo_url  }}">
                    <ul class="store-msg">
                        <li>店家姓名:{{ $shop->name }} <a href="javascript:" class="qrcode" data-target="#qrcodeModal" data-toggle="modal" data-url="{{ url('shop/' . $shop->id) }}">店铺二维码</a></li></li>
                        <li>联系人:{{ $shop->contact_person }}</li>
                        <li>最低配送额:￥{{ $shop->min_money }}</li>
                    </ul>
                </div>
            </div>
            <div class="address-panel col-xs-5">
                <ul>
                    <i class="icon icon-tel"></i>
                    <li class="address-panel-item">
                        <span class="panel-name">联系方式</span>
                        <span>{{ $shop->contact_info }}</span>
                    </li>
                </ul>
                <ul>
                    <i class="icon icon-seller"></i>
                    <li class="address-panel-item">
                        <span class="panel-name">店家地址</span>
                        <span>{{ $shop->address }}</span>
                    </li>
                </ul>
            </div>
            <div class="col-xs-2 text-right">
                <a class="btn btn-primary"  href="{{ url('personal/shop') }}">编辑</a>
            </div>
        </div>
        <div class="row home-orders-list">
            <div class="col-xs-3 item">
                <div class="item-wrap">
                    <div class="title">
                        <span>待审核</span>
                       <a href="{{ url('order-sell?status=non_confirm') }}"> <span class="order-prompt pull-right">订单</span></a>
                    </div>
                    <div class="quantity">{{ $waitConfirm }}</div>
                </div>
            </div>
            <div class="col-xs-3 item">
                <div class="item-wrap">
                    <div class="title">
                        <span>待发货</span>
                        <a href="{{ url('order-sell?status=non_send') }}"><span class="order-prompt pull-right">订单</span></a>
                    </div>
                    <div class="quantity">{{ $waitSend }}</div>
                </div>
            </div>
            <div class="col-xs-3 item">
                <div class="item-wrap">
                    <div class="title">
                        <span>待付款</span>
                        <a href="{{ url('order-sell?status=non_payment') }}"><span class="order-prompt pull-right">订单</span></a>
                    </div>
                    <div class="quantity">{{ $waitReceive }}</div>
                </div>
            </div>
            <div class="col-xs-3 item">
                <div class="item-wrap">
                    <div class="title">
                       <span>待收款</span>
                        <a href="{{ url('order-sell/wait-receive') }}"><span class="order-prompt pull-right">订单</span></a>
                    </div>
                    <div class="quantity">{{ $refund }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">当月销售图表</h3>
                    </div>
                    <div class="panel-body">
                        <div id="myChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" >
                <div class="panel panel-info home-notice">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-envelope-o" aria-hidden="true"></i> 系统公告</h3>
                    </div>
                    <div class="panel-body">
                        @foreach((new \App\Services\NoticeService)->getNotice() as $notice)
                            <div class="item">
                                <a class="content-title" href="javascript:" data-target="#noticeModal"
                                   data-toggle="modal"
                                   data-content="{{ $notice->content }}" title="{{ $notice->title }}">
                                    <p class="home-name">{{ $notice->title }}</p>
                                </a>
                                <span class="pull-right">{{ $notice->time }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">当月订单统计</h3>
                    </div>
                    <div class="panel-body">
                        <div id="myChart2"></div>
                    </div>
                </div>
            </div>
            {{--<div class="col-xs-4">--}}
                {{--<div class="panel panel-info home-msg-list">--}}
                    {{--<div class="panel-heading">--}}
                        {{--<h3 class="panel-title"><i class="fa fa-envelope-o" aria-hidden="true"></i> 消息列表</h3>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<div class="item" title="支付订单 用户xxx支付了订单110,总金额0.01元">--}}
                            {{--<p class="home-name">支付订单 用户xxx支付了订单110,总金额0.01元</p>--}}
                            {{--<span class="pull-right">2016-05-04 14:53</span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>



@stop
@section('js-lib')
    @parent
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $('.content-title').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            });
            $.ajax({
                url: '/api/v1/personal/order-data',
                method: 'get'
            }).done(function (data) {
                //完成订单信息
                var  finishedOrders = data.finishedOrders;
                //付款订单信息
                var  rereceivedOrders = data.receivedOrders;
                //分类统计信息
                var orderGoodsInfo = data.orderGoodsInfo;

                var date = new Date;
                var year = date.getFullYear();
                var month = date.getMonth() + 1;
                month = (month < 10 ? "0" + month : month);
                //当前年月
                var mydate = (year.toString() + '-' + month.toString());
                var d = new Date(year, month, 0);
                //当前月天数
                var days = d.getDate();
                //当月日期数组
                var daysArray = new Array();
                //当月完成订单数组
                var finishedOrderData = new Array();
                //当月付款订单总数
                var receivedOrderData = new Array();

                for (var i = 1; i <= days; i++) {
                    var nowday = i < 10 ? '0' + i : i;
                    daysArray.push(mydate + '-' + nowday);
                    var finishCount = 0;
                    var rereceivedCount = 0;
                    for (var j = 0; j < finishedOrders.length; j++) {
                        if (finishedOrders[j]['day'] == mydate + '-' + nowday) {
                            finishCount = finishedOrders[j]['count'];
                        }
                    }
                    for (var e = 0; e < rereceivedOrders.length; e++) {
                        if (rereceivedOrders[e]['receivedday'] == mydate + '-' + nowday) {
                            rereceivedCount = rereceivedOrders[e]['count'];
                        }
                    }
                    finishedOrderData.push(finishCount);
                    receivedOrderData.push(rereceivedCount);

                }
                //订单统计
                var option2 = {
                    title: {
                        text: '订单统计'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['付款单数', '完成单数']
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: daysArray
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: '付款单数',
                            type: 'line',
                            smooth: true,
                            data: receivedOrderData
                        },
                        {
                            name: '完成单数',
                            type: 'line',
                            smooth: true,
                            data: finishedOrderData
                        },

                    ]
                };
                myChart2.setOption(option2);

                orderGoodsInfo;
                var orderGoodsNames = new Array();
                var orderGoodsData = new Array();
                for(var i=0;i<orderGoodsInfo.length;i++){
                    alert(orderGoodsInfo[i]['name']);
                    var obj = {value: orderGoodsInfo[i]['sum'], name: orderGoodsInfo[i]['name']};
                    orderGoodsData.push(obj);
                    orderGoodsNames.push(orderGoodsInfo[i]['name']);
                }
                //月销售图表
                var option = {
                    title : {
                        text: '分类购买量统计',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                        data: orderGoodsNames
                    },
                    series : [
                        {
                            name: '商品销量',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '60%'],
                            data:orderGoodsData,
                            itemStyle: {
                                emphasis: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };
                myChart.setOption(option);
            });


            var myChart = echarts.init(document.getElementById('myChart'));
            var myChart2 = echarts.init(document.getElementById('myChart2'));




        })
    </script>
@stop

