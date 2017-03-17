@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '订单统计')

@section('top-title')
    <a href="{{ url('order-buy') }}">进货管理</a> >
    <span class="second-level">订单统计</span>
@stop

@section('right')
    <div class="row my-goods order-report margin-clear">
        <div class="col-sm-12 content">
            <form action="{{ url('order/statistics-of-buy') }}" method="get" autocomplete="off">
                <div class="col-sm-12 enter-item">
                    <input class="enter datetimepicker" name="start_at" placeholder="开始时间" type="text"
                           data-format="YYYY-MM-DD" value="{{ $startTime }}">至
                    <input class="enter datetimepicker" name="end_at" placeholder="结束时间" type="text"
                           data-format="YYYY-MM-DD" value="{{ $endTime }}">
                    <select class="enter" name="pay_type">
                        <option value="">全部方式</option>
                        @foreach(cons()->valueLang('pay_type') as $type=> $name)
                            <option value="{{ $type }}" {{ $type == $payType ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="enter" name="shop_name" placeholder="请输入卖家家名称"
                           value="{{ $shopName }}">
                    <button id="submitBtn" class="btn btn-blue-lighter search-by-get" type="submit">搜索</button>
                    <a id="export" href="{{ url('order/statistics-of-buy-export?' . http_build_query($data)) }}"
                       class="btn export-btn">统计导出</a>
                </div>
            </form>

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
                        <td class="first-td text-right">店铺名称</td>
                        <td class="2-1">
                            订单数
                            <p class="margin-clear prompt">(业务订单+自主订单)</p>
                        </td>
                        <td>
                            总金额
                            <p class="margin-clear prompt">(业务订单+自主订单)</p>
                        </td>
                        <td>实付金额</td>
                        <td>未付金额</td>
                        <td>联系方式</td>
                        <td>地址</td>
                        <td>操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orderStatisticsGroupName as $item)
                        <tr>
                            <td>{{ $item['shopName'] }}</td>
                            <td>{{ $item['orderCount'] }}
                                <p class="margin-clear prompt">
                                    ({{ $item['businessOrderCount'] }}+{{ $item['ownOrderCount'] }})
                                </p>
                            </td>
                            <td>{{ number_format($item['amount'], 2) }}
                                <p class="margin-clear prompt">
                                    ({{ number_format($item['businessOrderAmount'], 2) }}
                                    + {{ number_format($item['ownOrderAmount'], 2) }})
                                </p>
                            </td>
                            <td>{{ number_format($item['actualAmount'], 2) }}</td>
                            <td>{{ number_format($item['notPaidAmount'], 2) }}</td>
                            <td>{{ $item['contact'] }}</td>
                            <td>{{ $item['address'] }}</td>
                            <td>
                                <a href="javascript:" onclick="window.open ('{{ url('order/statistics-of-buy-user-detail?shop_id=' . $item['id']) . '&' . http_build_query(array_except($data , 'shop_name')) }}', 'newwindow', 'height=800, width=1000')">明细</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12  table-responsive tables">
                <p class="title-table">商品总计</p>
                <table class="MyTable3 table-scroll">
                    <thead>
                    <tr>
                        <td>商品名称</td>
                        <td>总进货量</td>
                        <td>总金额</td>
                        <td>平均单价</td>
                        <td>进货数量</td>
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
                </table>
            </div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            statisticsFunc();
            formSubmitByGet();
        })

    </script>
@stop