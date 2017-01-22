@extends('admin.master')

@section('subtitle' , '金额数据统计')

@include('includes.timepicker')

@section('right-container')
    @include('admin.operation.financial-nav')
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/operation-data/financial') }}" method="get"
              autocomplete="off">
            <a href="{{ url('admin/operation-data/financial?t=today') }}"
               class="time-format {{ array_get($data, 't') == 'today' ? 'active' : '' }}">今天</a>
            <a href="{{ url('admin/operation-data/financial?t=yesterday') }}"
               class="time-format {{ array_get($data, 't') == 'yesterday' ? 'active' : '' }}">昨天</a>
            <a href="{{ url('admin/operation-data/financial?t=week') }}"
               class="time-format {{ array_get($data, 't') == 'week' ? 'active' : '' }}">本周</a>
            <a href="{{ url('admin/operation-data/financial?t=month') }}"
               class="time-format {{ array_get($data, 't') == 'month' ? 'active' : '' }}">本月</a>
            <input type="text" name="begin_day" class="datetimepicker enter-control text-center"
                   data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}"/>
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="datetimepicker enter-control text-center" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}"/>
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="{{ url('admin/operation-data/financial-export?' . http_build_query($data)) }}"
               class="btn btn-border-blue control export">导出</a>
        </form>
        <div id="myTabContent" class="tab-content">
            <table class="table public-table table-bordered">
                <tr class="title-blue">
                    <th></th>
                    <th>渠道</th>
                    <th>下单总笔数</th>
                    <th>下单总金额</th>
                    <th>需支付金额</th>
                    <th>已完成支付金额</th>
                    <th>在线支付金额</th>
                    <th>现金支付金额</th>
                    <th>未完成支付金额</th>
                </tr>
                <tr>
                    <td rowspan="4" class="middle">终端商</td>
                    <td>自主订单</td>
                    <td>{{ number_format($retailerOwnerCount = $retailer['owner']['count']) }}</td>
                    <td>{{ number_format($retailerOwnerAmount = $retailer['owner']['amount'], 2) }}</td>
                    <td>{{ number_format($retailerOwnerAfterRebates = $retailer['owner']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($retailerOwnerCompleteAmount = $retailer['owner']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($retailerOwnerCompleteAmountByOnline = $retailer['owner']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($retailerOwnerCompleteAmountByOffline = bcsub($retailerOwnerCompleteAmount, $retailerOwnerCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($retailerOwnerNotAmount = bcsub($retailerOwnerAfterRebates, $retailerOwnerCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr>
                    <td>业务订单</td>
                    <td>{{ number_format($retailerBusinessCount = $retailer['business']['count']) }}</td>
                    <td>{{ number_format($retailerBusinessAmount = $retailer['business']['amount'], 2) }}</td>
                    <td>{{ number_format($retailerBusinessAfterRebates = $retailer['business']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($retailerBusinessCompleteAmount = $retailer['business']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($retailerBusinessCompleteAmountByOnline = $retailer['business']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($retailerBusinessCompleteAmountByOffline = bcsub($retailerBusinessCompleteAmount, $retailerBusinessCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($retailerBusinessNotAmount = bcsub($retailerBusinessAfterRebates, $retailerBusinessCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr>
                    <td>自提订单</td>
                    <td>{{ number_format($retailerPickUpCount = $retailer['pickUp']['count']) }}</td>
                    <td>{{ number_format($retailerPickUpAmount = $retailer['pickUp']['amount'], 2) }}</td>
                    <td>{{ number_format($retailerPickUpAfterRebates = $retailer['pickUp']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($retailerPickUpCompleteAmount = $retailer['pickUp']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($retailerPickUpCompleteAmountByOnline = $retailer['pickUp']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($retailerPickUpCompleteAmountByOffline = bcsub($retailerPickUpCompleteAmount, $retailerPickUpCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($retailerPickUpNotAmount = bcsub($retailerPickUpAfterRebates, $retailerPickUpCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr class="gray">
                    <td>总计</td>
                    <td>{{ number_format($retailerCount = $retailerOwnerCount + $retailerBusinessCount + $retailerPickUpCount) }}</td>
                    <td>{{ number_format($retailerAmount = $retailerOwnerAmount + $retailerBusinessAmount + $retailerPickUpAmount, 2) }}</td>
                    <td>{{ number_format($retailerAfterRebates  = $retailerOwnerAfterRebates + $retailerBusinessAfterRebates + $retailerPickUpAfterRebates, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmount = $retailerOwnerCompleteAmount + $retailerBusinessCompleteAmount + $retailerPickUpCompleteAmount, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmountByOnline = $retailerOwnerCompleteAmountByOnline + $retailerPickUpCompleteAmountByOnline + $retailerPickUpCompleteAmountByOnline, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmountByOffline = $retailerOwnerCompleteAmountByOffline + $retailerBusinessCompleteAmountByOffline + $retailerPickUpCompleteAmountByOffline, 2) }}</td>
                    <td>{{ number_format($retailerCompleteNotAmount = $retailerOwnerNotAmount + $retailerBusinessNotAmount + $retailerPickUpNotAmount, 2) }}</td>
                </tr>
                <tr>
                    <td rowspan="4" class="middle">批发商</td>
                    <td>自主订单</td>
                    <td>{{ number_format($wholesalerOwnerCount = $wholesaler['owner']['count']) }}</td>
                    <td>{{ number_format($wholesalerOwnerAmount = $wholesaler['owner']['amount'], 2) }}</td>
                    <td>{{ number_format($wholesalerOwnerAfterRebates = $wholesaler['owner']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($wholesalerOwnerCompleteAmount = $wholesaler['owner']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerOwnerCompleteAmountByOnline = $wholesaler['owner']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($wholesalerOwnerCompleteAmountByOffline = bcsub($wholesalerOwnerCompleteAmount, $wholesalerOwnerCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($wholesalerOwnerNotAmount = bcsub($wholesalerOwnerAfterRebates, $wholesalerOwnerCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr>
                    <td>业务订单</td>
                    <td>{{ number_format($wholesalerBusinessCount = $wholesaler['business']['count']) }}</td>
                    <td>{{ number_format($wholesalerBusinessAmount = $wholesaler['business']['amount'], 2) }}</td>
                    <td>{{ number_format($wholesalerBusinessAfterRebates = $wholesaler['business']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($wholesalerBusinessCompleteAmount = $wholesaler['business']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerBusinessCompleteAmountByOnline = $wholesaler['business']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($wholesalerBusinessCompleteAmountByOffline = bcsub($wholesalerBusinessCompleteAmount, $wholesalerBusinessCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($wholesalerBusinessNotAmount = bcsub($wholesalerBusinessAfterRebates, $wholesalerBusinessCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr>
                    <td>自提订单</td>
                    <td>{{ number_format($wholesalerPickUpCount = $wholesaler['pickUp']['count']) }}</td>
                    <td>{{ number_format($wholesalerPickUpAmount = $wholesaler['pickUp']['amount'], 2) }}</td>
                    <td>{{ number_format($wholesalerPickUpAfterRebates = $wholesaler['pickUp']['afterRebates'], 2) }}</td>
                    <td>{{ number_format($wholesalerPickUpCompleteAmount = $wholesaler['pickUp']['completeAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerPickUpCompleteAmountByOnline = $wholesaler['pickUp']['completeAmountByOnline'], 2) }}</td>
                    <td>{{ number_format($wholesalerPickUpCompleteAmountByOffline = bcsub($wholesalerPickUpCompleteAmount, $wholesalerPickUpCompleteAmountByOnline, 2), 2) }}</td>
                    <td>{{ number_format($wholesalerPickUpNotAmount = bcsub($wholesalerPickUpAfterRebates, $wholesalerPickUpCompleteAmount, 2), 2) }}</td>
                </tr>
                <tr class="gray">
                    <td>总计</td>
                    <td>{{ number_format($wholesalerCount = $wholesalerOwnerCount + $wholesalerBusinessCount + $wholesalerPickUpCount) }}</td>
                    <td>{{ number_format($wholesalerAmount = $wholesalerOwnerAmount + $wholesalerBusinessAmount + $wholesalerPickUpAmount, 2) }}</td>
                    <td>{{ number_format($wholesalerAfterRebates  = $wholesalerOwnerAfterRebates + $wholesalerBusinessAfterRebates + $wholesalerPickUpAfterRebates, 2) }}</td>
                    <td>{{ number_format($wholesalerCompleteAmount = $wholesalerOwnerCompleteAmount + $wholesalerBusinessCompleteAmount + $wholesalerPickUpCompleteAmount, 2) }}</td>
                    <td>{{ number_format($wholesalerCompleteAmountByOnline = $wholesalerOwnerCompleteAmountByOnline + $wholesalerPickUpCompleteAmountByOnline + $wholesalerPickUpCompleteAmountByOnline, 2) }}</td>
                    <td>{{ number_format($wholesalerCompleteAmountByOffline = $wholesalerOwnerCompleteAmountByOffline + $wholesalerBusinessCompleteAmountByOffline + $wholesalerPickUpCompleteAmountByOffline, 2) }}</td>
                    <td>{{ number_format($wholesalerCompleteNotAmount = $wholesalerOwnerNotAmount + $wholesalerBusinessNotAmount + $wholesalerPickUpNotAmount, 2) }}</td>
                </tr>
                <tr class="title-blue">
                    <td colspan="2">合计</td>
                    <td>{{ number_format($retailerCount + $wholesalerCount) }}</td>
                    <td>{{ number_format($retailerAmount + $wholesalerAmount, 2) }}</td>
                    <td>{{ number_format($retailerAfterRebates + $wholesalerAfterRebates, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmount + $wholesalerCompleteAmount, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmountByOnline + $wholesalerCompleteAmountByOnline, 2) }}</td>
                    <td>{{ number_format($retailerCompleteAmountByOffline + $wholesalerCompleteAmountByOffline, 2) }}</td>
                    <td>{{ number_format($retailerCompleteNotAmount + $wholesalerCompleteNotAmount, 2) }}</td>
                </tr>
            </table>
            <table class="table public-table table-bordered">
                <tr class="title-blue">
                    <th></th>
                    <th>渠道</th>
                    <th>已完成总笔数</th>
                    <th>已完成总金额</th>
                    <th>在线支付金额</th>
                    <th>现金支付金额</th>
                    <th>未完成总金额</th>
                    <th>未收款总金额</th>
                    <th>未配送总金额</th>
                </tr>
                <tr>
                    <td rowspan="3" class="middle">供应商</td>
                    <td>终端商</td>
                    <td>{{ number_format($supplierByRetailerCompleteCount = $supplierByRetailer['completeCount']) }}</td>
                    <td>{{ number_format($supplierByRetailerCompleteAmount = $supplierByRetailer['completeAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByRetailerCompleteAmountByOnlinePay = $supplierByRetailer['completeAmountByOnlinePay'], 2) }}</td>
                    <td>{{ number_format($supplierByRetailerCompleteAmountByOffPay = $supplierByRetailerCompleteAmount - $supplierByRetailerCompleteAmountByOnlinePay, 2) }}</td>
                    <td>{{ number_format($supplierByRetailerNotCompleteAmount = $supplierByRetailer['notCompleteAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByRetailerNotReceiveAmount = $supplierByRetailer['notReceiveAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByRetailerNotDeliveryAmount = $supplierByRetailer['notDeliveryAmount'], 2) }}</td>
                </tr>
                <tr>
                    <td>批发商</td>
                    <td>{{ number_format($supplierByWholesalerCompleteCount = $supplierByWholesaler['completeCount']) }}</td>
                    <td>{{ number_format($supplierByWholesalerCompleteAmount = $supplierByWholesaler['completeAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByWholesalerCompleteAmountByOnlinePay = $supplierByWholesaler['completeAmountByOnlinePay'], 2) }}</td>
                    <td>{{ number_format($supplierByWholesalerCompleteAmountByOffPay = $supplierByWholesalerCompleteAmount - $supplierByWholesalerCompleteAmountByOnlinePay, 2) }}</td>
                    <td>{{ number_format($supplierByWholesalerNotCompleteAmount = $supplierByWholesaler['notCompleteAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByWholesalerNotReceiveAmount = $supplierByWholesaler['notReceiveAmount'], 2) }}</td>
                    <td>{{ number_format($supplierByWholesalerNotDeliveryAmount = $supplierByWholesaler['notDeliveryAmount'], 2) }}</td>
                </tr>
                <tr class="gray">
                    <td>总计</td>
                    <td>{{ number_format($supplierCount = $supplierByRetailerCompleteCount + $supplierByWholesalerCompleteCount) }}</td>
                    <td>{{ number_format($supplierAmount = $supplierByRetailerCompleteAmount + $supplierByWholesalerCompleteAmount , 2) }}</td>
                    <td>{{ number_format($supplierOnlineAmount = $supplierByRetailerCompleteAmountByOnlinePay + $supplierByWholesalerCompleteAmountByOnlinePay , 2) }}</td>
                    <td>{{ number_format($supplierOfflineAmount = $supplierByRetailerCompleteAmountByOffPay + $supplierByWholesalerCompleteAmountByOffPay , 2) }}</td>
                    <td>{{ number_format($supplierNotCompleteAmount = $supplierByRetailerNotCompleteAmount + $supplierByWholesalerNotCompleteAmount , 2) }}</td>
                    <td>{{ number_format($supplierNotReceiveAmount = $supplierByRetailerNotReceiveAmount + $supplierByWholesalerNotReceiveAmount , 2) }}</td>
                    <td>{{ number_format($supplierNotDeliveryAmount = $supplierByRetailerNotDeliveryAmount + $supplierByWholesalerNotDeliveryAmount , 2) }}</td>
                </tr>
                <tr>
                    <td class="middle">批发商</td>
                    <td>终端商</td>
                    <td>{{ number_format($wholesalerByRetailerCompleteCount = $wholesalerByRetailer['completeCount']) }}</td>
                    <td>{{ number_format($wholesalerByRetailerCompleteAmount = $wholesalerByRetailer['completeAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerByRetailerCompleteAmountByOnlinePay = $wholesalerByRetailer['completeAmountByOnlinePay'], 2) }}</td>
                    <td>{{ number_format($wholesalerByRetailerCompleteAmountByOffPay = $wholesalerByRetailerCompleteAmount - $wholesalerByRetailerCompleteAmountByOnlinePay, 2) }}</td>
                    <td>{{ number_format($wholesalerByRetailerNotCompleteAmount = $wholesalerByRetailer['notCompleteAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerByRetailerNotReceiveAmount = $wholesalerByRetailer['notReceiveAmount'], 2) }}</td>
                    <td>{{ number_format($wholesalerByRetailerNotDeliveryAmount = $wholesalerByRetailer['notDeliveryAmount'], 2) }}</td>
                </tr>
                <tr class="title-blue">
                    <td colspan="2">合计</td>
                    <td>{{ number_format($supplierCount + $wholesalerByRetailerCompleteCount) }}</td>
                    <td>{{ number_format($supplierAmount + $wholesalerByRetailerCompleteAmount , 2) }}</td>
                    <td>{{ number_format($supplierOnlineAmount + $wholesalerByRetailerCompleteAmountByOnlinePay , 2) }}</td>
                    <td>{{ number_format($supplierOfflineAmount + $wholesalerByRetailerCompleteAmountByOffPay , 2) }}</td>
                    <td>{{ number_format($supplierNotCompleteAmount + $wholesalerByRetailerNotCompleteAmount , 2) }}</td>
                    <td>{{ number_format($supplierNotReceiveAmount + $wholesalerByRetailerNotReceiveAmount , 2) }}</td>
                    <td>{{ number_format($supplierNotDeliveryAmount + $wholesalerByRetailerNotDeliveryAmount , 2) }}</td>
                </tr>
            </table>
        </div>
        <div class="chart-wrap">
            <div id="myChart" class="chart"></div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        financial();
        $("[data-toggle='popover']").popover();
    </script>
@stop