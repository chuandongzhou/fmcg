@extends('index.manage-master')

@section('subtitle', '业务管理-陈列费发放情况')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <span class="second-level">陈列费发放情况</span>
                </div>
            </div>

            <div class="row salesman sales-details-panel">
                <div class="col-sm-12 form-group salesman-controls">
                    <form action="{{ url('business/display-info') }}" method="get">
                        <input class="control datetimepicker" data-format="YYYY-MM" type="text" name="month"
                               value="{{ $month }}">
                        <select class="control" name="salesman_id">
                            <option value="">请选择业务员</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ $salesman->id == $salesmanId ? 'selected' : '' }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                        <input class="control" type="text" name="name" value="{{ $name }}" placeholder="请输入客户名称">
                        <button type="submit" class="btn btn-blue-lighter search-by-get">查询</button>
                        <a class="btn btn-border-blue"
                           href="{{ url("business/display-info/export?month={$month}&name={$name}") }}">导出</a>
                    </form>
                </div>
                <div class="col-sm-12">
                    @foreach($customers as $customer)
                        <div class="table-list">
                            <table class="table table-center table-bordered margin-clear first">
                                <thead>
                                <tr>
                                    <th class="title-gray">客户编号</th>
                                    <th class="title-gray">店铺名称</th>
                                    <th class="title-gray">联系人</th>
                                    <th class="title-gray">联系电话</th>
                                    <th class="title-gray">营业地址</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{  $customer->number }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->contact }}</td>
                                    <td>{{ $customer->contact_information }}</td>
                                    <td>{{ $customer->business_address_name }}</td>
                                </tr>
                                </tbody>
                            </table>

                            @if($customer->display_type == cons('salesman.customer.display_type.mortgage'))
                                <table class="table table-center table-bordered margin-clear">
                                    <thead>
                                    <tr>
                                        <th colspan="10" class="text-center title">陈列费发放情况</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>时间</th>
                                        <th>商品名称</th>
                                        <th>商品单位</th>
                                        <th>应发商品数量</th>
                                        <th>实发商品数量</th>
                                        <th>剩余商品数量</th>
                                    </tr>
                                    @foreach($customer->mortgageGoods as $key => $mortgageGood)
                                        <tr>
                                            @if(!$key)
                                                <td rowspan="{{ $customer->mortgageGoods->count() }}">{{ $month }}</td>
                                            @endif
                                            <td>{{ $mortgageGood->goods_name }}</td>
                                            <td>{{ cons()->valueLang('goods.pieces', $mortgageGood->pieces) }}</td>
                                            <td>{{ $total = $mortgageGood->pivot->total }}</td>
                                            <td>{{ $mortgageGood->used }}</td>
                                            <td>{{ bcsub($total, $mortgageGood->used) }}</td>
                                        </tr>
                                    @endforeach
                                    @if(empty($customer->mortgageGoods))
                                        <tr>
                                            <td colspan="6">
                                                暂无
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div class="toggle-table">
                                    <table class="table table-bordered table-center margin-clear">
                                        <thead>
                                        <tr>
                                            <th class="title-gray">订单号</th>
                                            <th class="title-gray">业务员</th>
                                            <th class="title-gray">下单时间</th>
                                            <th class="title-gray">状态</th>
                                            <th class="title-gray">商品名称</th>
                                            <th class="title-gray">实发商品数</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($customer->orders as $key =>$order)
                                            @foreach($order['mortgages'] as $k=>$item)
                                                <tr>
                                                    @if($k == 0)
                                                        <td rowspan="{{ count($order['mortgages']) }}">{{ $order['id'] }}</td>
                                                        <td rowspan="{{ count($order['mortgages']) }}">{{ $order['salesmanName'] }}</td>
                                                        <td rowspan="{{ count($order['mortgages']) }}">{{ $order['time'] }}</td>
                                                        <td rowspan="{{ count($order['mortgages']) }}">{{ $order['status'] }}</td>
                                                    @endif
                                                    <td>{{ $item['name'] }}</td>
                                                    <td>{{ $item['used'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        @if(empty($customer->orders))
                                            <tr>
                                                <td colspan="6">
                                                    暂无
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <table class="table table-center table-bordered margin-clear">
                                    <thead>
                                    <tr>
                                        <th colspan="10" class="text-center title">陈列费发放情况</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>时间</th>
                                        <th>应发现金金额(元)</th>
                                        <th>实发现金金额(元)</th>
                                        <th>剩余现金金额(元)</th>
                                    </tr>
                                    <tr>
                                        <td>{{ $month }}</td>
                                        <td>{{ number_format($fee = $customer->display_fee, 2) }}</td>
                                        <td>{{ number_format($used =  $customer->displayLists->sum('used'), 2) }}</td>
                                        <td>{{ bcsub($fee, $used, 2) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="toggle-table">
                                    <table class="table table-bordered table-center margin-clear">
                                        <thead>
                                        <tr>
                                            <th class="title-gray">订单号</th>
                                            <th class="title-gray">业务员</th>
                                            <th class="title-gray">下单时间</th>
                                            <th class="title-gray">状态</th>
                                            <th class="title-gray">实发现金金额（元）</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($customer->displayLists->isEmpty())
                                            <tr>
                                                <td colspan="5">
                                                    暂无
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach($customer->displayLists as $item)
                                            <tr>
                                                <td>{{ $item->salesman_visit_order_id }}</td>
                                                <td>{{ $item->order->salesman_name }}</td>
                                                <td>{{ $item->order->created_at }}</td>
                                                <td>{{ $item->order->order_status_name }}</td>
                                                <td>{{ $item->used }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="toggle-panel text-center">
                                <a><i class="fa fa-angle-down"></i> 展开详情</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @parent
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            formSubmitByGet();
            $(".toggle-panel a").click(function () {
                var toggle_table = $(this).parents().siblings(".toggle-table"), self = $(this);
                if (toggle_table.is(":hidden")) {
                    toggle_table.slideDown();
                    self.html("<i class='fa fa-angle-up'></i> 收起详情");
                } else {
                    toggle_table.slideUp();
                    self.html("<i class='fa fa-angle-down'></i> 展开详情");
                }
            })
        })
    </script>
@stop
