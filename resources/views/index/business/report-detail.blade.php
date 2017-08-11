@extends('index.manage-master')
@include('includes.timepicker')
@include('includes.salesman-customer-route-map')
@section('subtitle', '业务管理-业务报告详细')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <a href="{{ url('business/report') }}">业务员报告</a> >
                    <span class="second-level">业务报表明细</span>
                </div>
            </div>
            <div class="row sales-details-panel">
                <div class="col-sm-12 form-group salesman-controls">
                    <a href="javascript:history.back()" class="btn btn-border-blue"><i class="iconfont icon-fanhui"></i>返回</a>
                    @if($isDay = ($startDate == $endDate))
                        <a class="btn btn-border-blue customer-map" href="javascript:"
                           data-target="#customerAddressMapModal"
                           data-toggle="modal">
                            <i class="fa fa-map-marker"></i> 拜访线路图
                        </a>
                    @endif
                    <a href="{{ url("business/report/{$salesman->id}/export?start_date={$startDate}&end_date={$endDate}") }}"
                       class="btn btn-border-blue"><i class="iconfont icon-xiazai"></i>下载打印</a>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <b>{{ $salesman->name }} - 业务报表</b>
                                <span>{{ $isDay ? $startDate : $startDate . '-' . $endDate }}</span>
                            </h3>
                        </div>
                        <div class="panel-container">
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <th>拜访客户数</th>
                                    <th>退货单数</th>
                                    <th>退货金额</th>
                                    <th>拜访订货单数</th>
                                    <th>拜访订货金额</th>
                                    <th>自主订货单数</th>
                                    <th>自主订货金额</th>
                                    <th>总订货单数</th>
                                    <th>总订货金额</th>
                                    <th>总应付金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ $visitStatistics['customerCount'] }}</td>
                                    <td>{{ $visitStatistics['returnOrderCount'] or 0 }}</td>
                                    <td class="red">{{ $visitStatistics['returnOrderAmount'] or 0 }}</td>
                                    <td>{{ $visitStatistics['visitOrderCount'] or 0  }}</td>
                                    <td class="red">{{ $visitStatistics['visitOrderAmount'] or 0 }}</td>
                                    <td>{{ $visitStatistics['ownOrderCount'] }}</td>
                                    <td class="red">{{ $visitStatistics['ownOrderAmount'] }}</td>
                                    <td>{{ $visitStatistics['totalCount'] }}</td>
                                    <td class="red">{{ $visitStatistics['totalAmount'] }}</td>
                                    <td class="red">{{ bcsub($visitStatistics['totalAmount'],$visitStatistics['ownOrderDisplayFeeAmount'] + $visitStatistics['visitOrderDisplayFeeAmount'] + $visitStatistics['ownOrderCouponAmount'] + $visitStatistics['visitOrderCouponAmount'],2)}}
                                        </br> 优惠券:{{bcadd($visitStatistics['ownOrderCouponAmount'] ,$visitStatistics['visitOrderCouponAmount'],2)}}
                                        </br>陈列费:{{bcadd($visitStatistics['ownOrderDisplayFeeAmount'] ,$visitStatistics['visitOrderDisplayFeeAmount'],2)}}</td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="public-table MyTable1 table-scroll business-tables">
                                <thead>
                                <tr>
                                    @if($isDay)
                                        <th>拜访顺序</th>
                                    @endif
                                    <th>客户编号</th>
                                    <th>客户名称</th>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>营业地址</th>
                                    @if($isDay)
                                        <th>提交地址</th>
                                        <th>拜访时间</th>
                                    @endif
                                    <th>拜访次数</th>
                                    <th>订货单数</th>
                                    <th>订货单金额</th>
                                    <th>退货单数</th>
                                    <th>退货单金额</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $visitList as $key=>$visit)
                                    <tr>
                                        @if($isDay)
                                            <td>{{ $key + 1 }}</td>
                                        @endif
                                        <td>{{ $visit['id'] }}</td>
                                        <td>{{ $visit['name'] }}</td>
                                        <td>{{ $visit['contact'] }}</td>
                                        <td>{{ $visit['contactInfo'] }}</td>
                                        <td>{{ $visit['businessAddress'] }}</td>
                                        @if($isDay)
                                            <td>
                                                {{ $visit['commitAddress'] }}
                                                <input type="hidden" class="map-data"
                                                       data-business-lng="{{ $visit['business_address_lng'] }}"
                                                       data-business-lat="{{ $visit['business_address_lat'] }}"
                                                       data-lng="{{ $visit['lng'] }}"
                                                       data-lat="{{ $visit['lat'] }}"
                                                       data-number="{{ $visit['visit_id'] }}"
                                                       data-name="{{ $visit['name'] }}"
                                                >
                                            </td>
                                            <td>{{ $visit['visitTime'] }}</td>
                                        @endif
                                        <td>{{ $visit['visitCount'] }}</td>
                                        <td>{{ $visit['orderCount'] }}</td>
                                        <td>{{ $visit['orderAmount'] }}</td>
                                        <td>{{ $visit['returnOrderCount'] }}</td>
                                        <td>{{ $visit['returnOrderAmount'] }}</td>
                                        <td>
                                            <a href="javascript:"
                                               onclick="window.open ('{{ url("business/report/{$salesman->id}/customer-detail?start_date={$startDate}&end_date={$endDate}&customer_id={$visit['id']}") }}', 'newwindow', 'height=600, width=1000')">明细</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{--@if(auth()->user()->type < cons('user.type.maker'))--}}
                        <div class="panel-container">
                            <p class="title-table">自主订单</p>
                            <table class="table business-table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <th>客户编号</th>
                                    <th>客户名称</th>
                                    <th>同步时间</th>
                                    <th>订单ID</th>
                                    <th>订单状态</th>
                                    <th>订单金额</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ownOrders as $ownOrder)
                                    <tr>
                                        <td>{{ $ownOrder->salesman_customer_id }}</td>
                                        <td>{{ $ownOrder->customer_name }}</td>
                                        <td>{{ $ownOrder->created_at }}</td>
                                        <td>{{ $ownOrder->order_id }}</td>
                                        <td>{{ $ownOrder->order_status_name }}</td>
                                        <td>{{ $ownOrder->amount }}</td>
                                        <td><a href="{{ url('order-sell/detail?order_id=' . $ownOrder->order_id) }}"
                                               target="_blank">明细</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{--@endif--}}


                        <div class="text-right">
                            <ul class="pagination">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        jQuery.browser = {};
        (function () {
            jQuery.browser.msie = false;
            jQuery.browser.version = 0;
            if (navigator.userAgent.match(/MSIE ([0-9]+)./)) {
                jQuery.browser.msie = true;
                jQuery.browser.version = RegExp.$1;
            }
        })();
        $(function () {
            var tableWidth = $(".table-scroll").parents("div").width();
            FixTable("MyTable1", 1, tableWidth, 400);

        });
        var customerMapData = function () {
            var mapData = [];
            $('.business-tables  .map-data').each(function () {
                var obj = $(this), data = [];
                data['lng'] = obj.data('lng');
                data['lat'] = obj.data('lat');
                data['businessLng'] = obj.data('businessLng');
                data['businessLat'] = obj.data('businessLat');
                data['number'] = '序号' + obj.data('number');
                data['name'] = obj.data('name');
                mapData.push(data);
            });
            return mapData;
        };
        tablePage($('.business-table'), $('.pagination'));
    </script>
@stop
