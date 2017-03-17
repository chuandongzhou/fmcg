@extends('master')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@stop

@section('body')
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
    <div class="container">
        <div class="row order-report report-detail margin-clear">
            <div class="col-sm-12 content">
                <div class="col-sm-12 tables">
                    <p class="title-table">订单总计</p>
                    <table class="MyTable1 table-scroll">
                        <thead>
                        <tr>
                            <td></td>
                            <td>订单数</td>
                            <td>总金额</td>
                            <td>已付金额</td>
                            <td>未付金额</td>
                            <td>在线支付订单数</td>
                            <td>在线支付金额</td>
                            <td>货到付款订单数</td>
                            <td>货到付款金额</td>
                            <td>自提订单数</td>
                            <td>自提订单金额</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>自主订单</td>
                            <td>{{ number_format($ownOrdersStatisticsCount = $ownOrdersStatistics['count']) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsAmount = $ownOrdersStatistics['amount'], 2) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsActualAmount = $ownOrdersStatistics['actualAmount'], 2) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsNotPaidAmount = $ownOrdersStatistics['notPaidAmount'], 2) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsOnlinePayCount = $ownOrdersStatistics['onlinePayCount']) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsOnlinePayAmount = $ownOrdersStatistics['onlinePayAmount'], 2) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsCodPayCount = $ownOrdersStatistics['codPayCount']) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsCodPayAmount = $ownOrdersStatistics['codPayAmount'], 2) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsPickUpCount = $ownOrdersStatistics['pickUpCount']) }}</td>
                            <td>{{ number_format($ownOrdersStatisticsPickUpAmount = $ownOrdersStatistics['pickUpAmount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>业务订单</td>
                            <td>{{ number_format($businessOrdersStatisticsCount = $businessOrdersStatistics['count']) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsAmount = $businessOrdersStatistics['amount'], 2) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsActualAmount = $businessOrdersStatistics['actualAmount'], 2) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsNotPaidAmount = $businessOrdersStatistics['notPaidAmount'], 2) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsOnlinePayCount = $businessOrdersStatistics['onlinePayCount']) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsOnlinePayAmount = $businessOrdersStatistics['onlinePayAmount'], 2) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsCodPayCount = $businessOrdersStatistics['codPayCount']) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsCodPayAmount = $businessOrdersStatistics['codPayAmount'], 2) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsPickUpCount = $businessOrdersStatistics['pickUpCount']) }}</td>
                            <td>{{ number_format($businessOrdersStatisticsPickUpAmount = $businessOrdersStatistics['pickUpAmount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>合计</td>
                            <td>{{ bcadd($ownOrdersStatisticsCount,$businessOrdersStatisticsCount) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsAmount, $businessOrdersStatisticsAmount, 2) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsActualAmount, $businessOrdersStatisticsActualAmount, 2) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsNotPaidAmount, $businessOrdersStatisticsNotPaidAmount, 2) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsOnlinePayCount, $businessOrdersStatisticsOnlinePayCount) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsOnlinePayAmount, $businessOrdersStatisticsOnlinePayAmount, 2) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsCodPayCount, $businessOrdersStatisticsCodPayCount) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsCodPayAmount, $businessOrdersStatisticsCodPayAmount, 2) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsPickUpCount, $businessOrdersStatisticsPickUpCount) }}</td>
                            <td>{{ bcadd($ownOrdersStatisticsPickUpAmount, $businessOrdersStatisticsPickUpAmount, 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="MyTable2 table-scroll">
                        <thead>
                        <tr>
                            <td class="first-td text-right">订单号</td>
                            <td class="2-1">订单类型</td>
                            <td>订单金额</td>
                            <td>订单状态</td>
                            <td>支付方式</td>
                            <td>下单时间</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->type_name }}</td>
                                <td>{{ $order->after_rebates_price }}</td>
                                <td>{{ $order->status_name }}</td>
                                <td>{{ $order->pay_type_name }}</td>
                                <td>{{ $order->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12  tables">
                    <p class="title-table">商品总计</p>
                    <table class="table table-bordered table-goods-statistics">
                        <thead>
                        <tr>
                            <td>商品名称</td>
                            <td>总进货量</td>
                            <td>总金额</td>
                            <td>平均单价</td>
                            <td>出货数量</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orderGoodsStatistics as $item)
                            @foreach($item['pieces'] as $piece=> $value)
                                <tr>
                                    @if($value == head($item['pieces']))
                                        <td rowspan="{{ $rowspan = count($item['pieces']) }}">{{ $item['name'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $item['num'] }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ number_format($item['amount'], 2) }}</td>
                                    @endif
                                    <td>{{ number_format(bcdiv($value['amount'], $value['num'], 2), 2) . '/' . cons()->valueLang('goods.pieces', $piece) }}</td>
                                    <td>{{ $value['num'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                        <tfoot>
                        <td colspan="5" class="text-center">
                            <div class="text-right">
                                <ul class="pagination management-pagination">
                                </ul>
                            </div>
                        </td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </body>
@stop


@section('js')
    <script type="text/javascript">
        jQuery.browser = {};
        (function () {
            jQuery.browser.msie = false;
            jQuery.browser.version = 0;
            if (navigator.userAgent.match(/MSIE ([0-9]+)./)) {
                jQuery.browser.msie = true;
                jQuery.browser.version = RegExp.$1;
            }
            var table_width = $(".table-scroll").parents("div").width();
            FixTable("MyTable1", 1, 1050, 200);
            FixTable("MyTable2", 1, 1050, 200);
            FixTable("MyTable3", 1, 1050, 200);
        })();
        tablePage($('.table-goods-statistics'), $('.pagination'));
    </script>
@stop

