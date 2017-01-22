@extends('admin.master')

@section('subtitle' , '成交金额统计')

@include('includes.timepicker')

@section('right-container')
    @include('admin.operation.financial-nav')
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/operation-data/complete-amount') }}" method="get"
              autocomplete="off">
            <input type="text" class="enter-control" name="name" placeholder="请输入出售商名称" value="{{ $name }}">
            <a href="{{ url('admin/operation-data/complete-amount?t=today') }}" class="time-format {{ array_get($data, 't') == 'today' ? 'active' : '' }}">今天</a>
            <a href="{{ url('admin/operation-data/complete-amount?t=yesterday') }}" class="time-format {{ array_get($data, 't') == 'yesterday' ? 'active' : '' }}">昨天</a>
            <a href="{{ url('admin/operation-data/complete-amount?t=week') }}" class="time-format {{ array_get($data, 't') == 'week' ? 'active' : '' }}">本周</a>
            <a href="{{ url('admin/operation-data/complete-amount?t=month') }}" class="time-format {{ array_get($data, 't') == 'month' ? 'active' : '' }}">本月</a>
            <input type="text" name="begin_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}">
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}">

            <input type="button" class="btn btn-blue control search-by-get" value="查询"/>
            <a href="{{ url('admin/operation-data/complete-amount-export?' . http_build_query($data)) }}" class="btn btn-border-blue control export">导出</a>
        </form>
        <div id="myTabContent" class="tab-content">
            @if(is_null($name))
                <table class="table money-table table-bordered">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>成交订单笔数</th>
                        <th>成交总金额（元）</th>
                        <th>在线收款金额（元）</th>
                        <th>线下收款金额（元）</th>
                        <th>pos收款金额（元）</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td rowspan="2" style="vertical-align: middle">供应商</td>
                        <td>{{ $supplierForWholesalerCount = $supplier['wholesaler']['count'] }}（批发）</td>
                        <td>{{ number_format( $supplierForWholesalerAmount = $supplier['wholesaler']['amount'],2) }}</td>
                        <td>{{ number_format( $supplierForWholesalerOnline = $supplier['wholesaler']['onlineAmount'],2) }}</td>
                        <td>{{ number_format( $supplierForWholesalerOffline =  $supplier['wholesaler']['offAmount'],2) }}</td>
                        <td>{{ number_format( $supplierForWholesalerPos =  $supplier['wholesaler']['posAmount'],2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ $supplierForRetailerCount = $supplier['retailer']['count'] }}（终端）</td>
                        <td>{{ number_format( $supplierForRetailerAmount = $supplier['retailer']['amount'],2) }}</td>
                        <td>{{ number_format( $supplierForRetailerOnline = $supplier['retailer']['onlineAmount'],2) }}</td>
                        <td>{{ number_format( $supplierForRetailerOffline =  $supplier['retailer']['offAmount'],2) }}</td>
                        <td>{{ number_format( $supplierForRetailerPos =  $supplier['retailer']['posAmount'],2) }}</td>
                    </tr>
                    <tr>
                        <td>批发商</td>
                        <td>{{ $retailerOrderCount = $wholesaler['count'] }}</td>
                        <td>{{ number_format( $retailerOrderAmount = $wholesaler['amount'],2) }}</td>
                        <td>{{ number_format( $retailerByOnline = $wholesaler['onlineAmount'],2) }}</td>
                        <td>{{ number_format( $retailerByOffline = $wholesaler['offAmount'],2) }}</td>
                        <td>{{ number_format( $retailerByPos = $wholesaler['posAmount'],2) }}</td>
                    </tr>
                    <tr>
                        <td>总计</td>
                        <td>{{ number_format($supplierForWholesalerCount + $supplierForRetailerCount + $retailerOrderCount) }}</td>
                        <td>{{ number_format($supplierForWholesalerAmount + $supplierForRetailerAmount + $retailerOrderAmount, 2) }}</td>
                        <td>{{ number_format($supplierForWholesalerOnline + $supplierForRetailerOnline + $retailerByOnline, 2) }}</td>
                        <td>{{ number_format($supplierForWholesalerOffline + $supplierForRetailerOffline + $retailerByOffline, 2) }}</td>
                        <td>{{ number_format($supplierForWholesalerPos + $supplierForRetailerPos + $retailerByPos , 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
            <table class="table money-table table-bordered shop-group">
                <thead>
                <tr>
                    <th>出售商名称</th>
                    <th>成交笔数</th>
                    <th>成交总金额(元)</th>
                    <th>在线收款金额(元)</th>
                    <th>线下收款金额(元)</th>
                    <th>pos收款金额(元)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($shopGroup as $shopName => $shop)
                    <tr>
                        <td>{{ $shopName }}（{{ cons()->lang('user.type')[$shop['type']] }}）</td>
                        <td>{{ array_get($shop, 'orderCount', 0) }}</td>
                        <td>{{ number_format(array_get($shop, 'orderAmount', 0), 2) }}</td>
                        <td>{{ number_format(array_get($shop, 'onLinePay', 0), 2) }}</td>
                        <td>{{ number_format(array_get($shop, 'offLinePay', 0), 2) }}</td>
                        <td>{{ number_format(array_get($shop, 'posPay', 0), 2) }}</td>
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
        tablePage($('.shop-group'), $('.pagination'));
    </script>
@stop