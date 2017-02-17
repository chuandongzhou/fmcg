@extends('index.menu-master')
@section('subtitle', '个人中心-店铺介绍')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> ><span class="second-level"> 个人中心</span>
@stop
@include('includes.qrcode')
@include('includes.notice')
@include('includes.timepicker')
@section('right')
    <div class="row store personal-store">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">店铺信息</h3>
                </div>
                <div class="panel-container clearfix">
                    <div class="col-xs-11">
                        <div class="store-panel clearfix">
                            <img class="avatar" src="{{ $shop->logo_url  }}" height="150" width="150">
                            <div class="store-msg">
                                <div class="store-name">
                                    <div class="item"><span class="panel-name">店铺名称 : </span>{{ $shop->name }}</div>
                                    @if($type!=cons('user.type.retailer'))
                                        <div class="item count-panel">
                                            <div class="pull-left left-panel">
                                                <span class="panel-name">店铺销量 : </span>{{ $shop->sales_volume }}
                                            </div>
                                            <div>
                                                <span class="panel-name">共</span> {{ $shop->goods_count }}
                                                <span class="panel-name">件商品</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="address-panel clearfix">
                                    @if($type!=cons('user.type.retailer'))
                                        <ul>
                                            <i class="icon qr-code"></i>
                                            <li class="address-panel-item">
                                                <span class="panel-name">店铺</span>
                                                <span>二维码</span>

                                                <div class="shop-code">
                                                    <img src="{{ (new \App\Services\ShopService())->qrcode($shop->id,150) }}">
                                                    <span>扫一扫 进入手机店铺</span>
                                                </div>
                                            </li>
                                        </ul>
                                    @endif
                                    <ul>
                                        <i class="icon iconfont icon-lianxiren"></i>
                                        <li class="address-panel-item">
                                            <span class="panel-name">联系人</span>
                                            <span>{{ $shop->contact_person }}</span>
                                        </li>
                                    </ul>
                                    <ul>
                                        <i class="icon iconfont icon-dianhua"></i>
                                        <li class="address-panel-item">
                                            <span class="panel-name">联系方式</span>
                                            <span>{{ $shop->contact_info }}</span>
                                        </li>
                                    </ul>
                                    @if($type!=cons('user.type.retailer'))
                                        <ul>
                                            <i class="icon iconfont icon-peisong"></i>
                                            <li class="address-panel-item">
                                                <span class="panel-name">最低配送额</span>
                                                <span>￥{{ $shop->min_money }}</span>
                                            </li>
                                        </ul>
                                    @endif


                                    <ul>
                                        <i class="icon iconfont icon-dizhi"></i>
                                        <li class="address-panel-item">
                                            <span class="panel-name">店家地址</span>
                                            <span>{{ $shop->address }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-left padding-clear">
                        <a class="btn editor-btn" href="{{ url('personal/shop') }}"><i class="iconfont icon-xiugai"></i>
                            编辑</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row home-orders-list">
        <div class="col-xs-3 item">
            <div class="item-wrap">
                <div class="clearfix">
                    <div class="pull-left icon-panel">
                        <i class="iconfont icon-daiqueren"></i>
                    </div>
                    <div class="pull-right">
                        <div class="title">
                            <span>待确认</span>
                        </div>
                        <div class="quantity">{{ $waitConfirm }}</div>
                    </div>
                </div>
                <a href="{{ $type==cons('user.type.retailer')?url('order-buy?status=non_confirm'):url('order-sell?status=non_confirm') }}">
                    <div class="check-btn">
                        查看订单
                        <i class="iconfont icon-youjiantou"></i>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xs-3 item">
            <div class="item-wrap">
                <div class="clearfix">
                    <div class="pull-left icon-panel">
                        <i class="iconfont icon-wodedingdan16"></i>
                    </div>
                    <div class="pull-right">
                        <div class="title">
                            <span>待发货</span>
                        </div>
                        <div class="quantity">{{ $waitSend }}</div>
                    </div>
                </div>
                <a href="{{ $type==cons('user.type.retailer')? url('order-buy?status=non_send'):url('order-sell?status=non_send') }}">
                    <div class="check-btn">
                        查看订单
                        <i class="iconfont icon-youjiantou"></i>
                    </div>
                </a>

            </div>
        </div>
        <div class="col-xs-3 item">
            <div class="item-wrap">
                <div class="clearfix">
                    <div class="pull-left icon-panel">
                        <i class="iconfont icon-shape"></i>
                    </div>
                    <div class="pull-right">
                        <div class="title">
                            <span>待付款</span>
                        </div>
                        <div class="quantity">{{ $waitReceive }}</div>
                    </div>
                </div>
                <a href="{{ $type==cons('user.type.retailer')?url('order-buy/wait-pay'):url('order-sell?status=non_payment') }}">
                    <div class="check-btn">
                        查看订单
                        <i class="iconfont icon-youjiantou"></i>
                    </div>
                </a>

            </div>

        </div>
        <div class="col-xs-3 item">
            <div class="item-wrap">
                <div class="clearfix">
                    <div class="pull-left icon-panel">
                        <i class="iconfont icon-daishouhuo"></i>
                    </div>
                    <div class="pull-right">
                        <div class="title">
                            <span>{{ $type==cons('user.type.retailer')?'待收货':'待收款' }}</span>
                        </div>
                        <div class="quantity">{{ $refund }}</div>
                    </div>
                </div>
                <a href="{{ $type==cons('user.type.retailer')?url('order-buy/wait-receive'):url('order-sell/wait-receive') }}">
                    <div class="check-btn">
                        查看订单
                        <i class="iconfont icon-youjiantou"></i>
                    </div>
                </a>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">当月进货图表</h3>
                </div>
                <div class="panel-container">
                    <div id="myChart" class="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="panel panel-default home-notice">
                <div class="panel-heading">
                    <h3 class="panel-title"> 系统公告</h3>
                </div>
                <div class="panel-container">
                    @foreach((new \App\Services\NoticeService)->getNotice() as $notice)
                        <div class="item">
                            <a class="content-title" href="javascript:" data-target="#noticeModal"
                               data-toggle="modal"
                               data-content="{{ $notice->content }}" title="{{ $notice->title }}">
                                <div class="home-name">{{ $notice->title }}</div>
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
                <div class="panel-container">
                    <div class="check-year">
                        选择年份 ：
                        <input class="inline-control datetimepicker" type="text" name="date"
                               placeholder="日期"
                               data-format="YYYY-MM" id="date">
                    </div>
                    <div id="myChart2" class="chart">

                    </div>
                </div>
            </div>
        </div>
        <!--<div class="col-xs-4">-->
        <!--<div class="panel panel-info home-msg-list">-->
        <!--<div class="panel-heading">-->
        <!--<h3 class="panel-title"><i class="fa fa-envelope-o" aria-hidden="true"></i> 消息列表</h3>-->
        <!--</div>-->
        <!--<div class="panel-container">-->
        <!--<div class="item" title="支付订单 用户xxx支付了订单110,总金额0.01元">-->
        <!--<div class="home-name">支付订单 用户xxx支付了订单110,总金额0.01元</div>-->
        <!--<span class="pull-right">2016-05-04 14:53</span>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
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
            var myChart = echarts.init(document.getElementById('myChart'));
            var myChart2 = echarts.init(document.getElementById('myChart2'));

            //系统公告点击事件
            $('.content-title').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            });
            //日期变化事件
            $("#date").on("dp.change", function (e) {
                if (e.oldDate != null) {
                    countData($('#date').val());
                }
            });
            //当前年月
            var date = new Date;
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            month = (month < 10 ? "0" + month : month);
            //首次加载订单统计数据
            countData(year.toString() + '-' + month.toString());
            //获取订单统计数据并加载
            function countData(month) {
                $.ajax({
                    url: '/api/v1/personal/order-data?month=' + month,
                    method: 'get'
                }).done(function (data) {
                    //完成订单信息
                    var finishedOrders = data.finishedOrders;
                    //付款订单信息
                    var rereceivedOrders = data.receivedOrders;
                    //分类统计信息
                    var date = new Date(data.selectMonth);
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    month = (month < 10 ? "0" + month : month);
                    //当前年月
                    var mydate = (year.toString() + '-' + month.toString());
                    $('#date').val(mydate);
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
                            text: ''
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

                });
            }

            //销售（进货）统计
            var type = '{!! $type !!}';
            var name = type == 1 ? '商品购买量' : '商品销量';
            var totalName = type == 1 ? '分类购买量统计' : '分类销售量统计';
            var orderGoodsNames = new Array();
            var orderGoodsData = new Array();
            var orderGoodsInfo = JSON.parse('{!! $orderGoodsInfo !!}');

            for (var i = 0; i < orderGoodsInfo.length; i++) {
                var obj = {value: orderGoodsInfo[i]['sum'], name: orderGoodsInfo[i]['name']};
                orderGoodsData.push(obj);
                orderGoodsNames.push(orderGoodsInfo[i]['name']);
            }

            //月销售图表
            var option = {
                title: {
                    text: totalName,
                    x: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: orderGoodsNames.length > 8 ? '' : orderGoodsNames
                },
                series: [
                    {
                        name: name,
                        type: 'pie',
                        radius: '55%',
                        center: ['50%', '60%'],
                        data: orderGoodsData,
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

        })
    </script>
@stop

