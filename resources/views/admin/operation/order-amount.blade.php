@extends('admin.master')

@section('subtitle' , '下单金额统计')

@include('includes.timepicker')

@section('right-container')
    @include('admin.operation.financial-nav')
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/operation-data/order-amount') }}" method="get"
              autocomplete="off">
            {{--<select class="control" name="type">--}}
            {{--<option value="">选择购买商</option>--}}
            {{--@foreach(array_except(cons()->valueLang('user.type'), 3) as $type => $value)--}}
            {{--<option value="{{ $type }}">{{ $value }}</option>--}}
            {{--@endforeach--}}
            {{--</select>--}}
            <input type="text" class="enter-control" name="name" placeholder="请输入购买商名称" value="{{ $name }}">
            <a href="{{ url('admin/operation-data/order-amount?t=today') }}" class="time-format">今天</a>
            <a href="{{ url('admin/operation-data/order-amount?t=yesterday') }}" class="time-format">昨天</a>
            <a href="{{ url('admin/operation-data/order-amount?t=week') }}" class="time-format">本周</a>
            <a href="{{ url('admin/operation-data/order-amount?t=month') }}" class="time-format">本月</a>
            <input type="text" name="begin_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}">
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}">

            <input type="button" class="btn btn-blue control search-by-get" value="查询"/>
            <a href="{{ url('admin/operation-data/order-amount-export?' . http_build_query($data)) }}" class="btn btn-border-blue control export">导出</a>
        </form>
        <div id="myTabContent" class="tab-content">
            @if(is_null($name))
                <table class="table money-table table-bordered">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>下单笔数</th>
                        <th>下单总金额(元)</th>
                        <th>在线支付金额(元)</th>
                        <th>线下支付金额(元)</th>
                        <th>已完成支付金额(元)</th>
                        <th>未完成支付金额(元)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>终端商</td>
                        <td>{{ $retailerOrderCount = $retailer['orderCount'] }}</td>
                        <td>{{ number_format( $retailerOrderAmount = $retailer['orderAmount'],2) }}</td>
                        <td>{{ number_format( $retailerOrderPaidByOnline = $retailer['orderPaidByOnline'],2) }}</td>
                        <td>{{ number_format( $retailerOrderPaidByOffline = $retailer['orderPaidByOffline'],2) }}</td>
                        <td>{{ number_format( $retailerComplete = bcadd($retailerOrderPaidByOnline,$retailerOrderPaidByOffline,2),2) }}</td>
                        <td>{{ number_format( $retailerNotComplete = bcsub($retailerOrderAmount,bcadd($retailerOrderPaidByOnline,$retailerOrderPaidByOffline,2), 2), 2) }}</td>
                    </tr>
                    <tr>
                        <td>批发商</td>
                        <td>{{ $wholesalerOrderCount = $wholesaler['orderCount'] }}</td>
                        <td>{{ number_format( $wholesalerOrderAmount = $wholesaler['orderAmount'],2) }}</td>
                        <td>{{ number_format( $wholesalerOrderPaidByOnline = $wholesaler['orderPaidByOnline'],2) }}</td>
                        <td>{{ number_format( $wholesalerOrderPaidByOffline = $wholesaler['orderPaidByOffline'],2) }}</td>
                        <td>{{ number_format( $wholesalerComplete = bcadd($wholesalerOrderPaidByOnline,$wholesalerOrderPaidByOffline,2),2) }}</td>
                        <td>{{ number_format( $wholesalerNotComplete = bcsub($wholesalerOrderAmount,bcadd($wholesalerOrderPaidByOnline,$wholesalerOrderPaidByOffline,2), 2), 2) }}</td>
                    </tr>
                    <tr>
                        <td>总计</td>
                        <td>{{ bcadd($retailerOrderCount, $wholesalerOrderCount) }}</td>
                        <td>{{ bcadd($retailerOrderAmount, $wholesalerOrderAmount, 2) }}</td>
                        <td>{{ bcadd($retailerOrderPaidByOnline, $wholesalerOrderPaidByOnline, 2) }}</td>
                        <td>{{ bcadd($retailerOrderPaidByOffline, $wholesalerOrderPaidByOffline, 2) }}</td>
                        <td>{{ bcadd($retailerComplete, $wholesalerComplete, 2) }}</td>
                        <td>{{ bcadd($retailerNotComplete, $wholesalerNotComplete, 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
            <table class="table money-table table-bordered shop-group">
                <thead>
                <tr>
                    <th>购买商名称</th>
                    <th>下单笔数</th>
                    <th>下单总金额(元)</th>
                    <th>在线支付金额(元)</th>
                    <th>线下支付金额(元)</th>
                    <th>已完成支付金额(元)</th>
                    <th>未完成支付金额(元)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($shopGroup as $shopName => $shop)
                    <tr>
                        <td>{{ $shopName }}（{{ cons()->lang('user.type')[$shop['type']] }}）</td>
                        <td>{{ array_get($shop, 'orderCount', 0) }}</td>
                        <td>{{ number_format(array_get($shop, 'orderAmount', 0), 2) }}</td>
                        <td>{{ number_format($onlinePay = array_get($shop, 'onLinePay', 0), 2) }}</td>
                        <td>{{ number_format($offlinePay = array_get($shop, 'offLinePay', 0), 2) }}</td>
                        <td>{{ number_format(bcadd($onlinePay, $offlinePay, 2), 2) }}</td>
                        <td>{{ number_format(array_get($shop, 'notPaid', 0), 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        <div class="text-right">
            <ul class="pagination management-pagination">
            </ul>
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
        formSubmitByGet();
        tablePage($('.shop-group'), 10, $('.pagination'));
    </script>
@stop