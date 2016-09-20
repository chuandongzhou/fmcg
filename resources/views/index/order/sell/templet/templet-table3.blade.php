@extends('master')
@section('title', '订单打印 | 订百达 - 订货首选')
@section('body')
    @yield('container')
    <div class="container temple-table temple-table-y">
        <div class="row">
            <div class="col-xs-12 text-center">
                <h2 class="title">{{ $order->shop->name }} 送货单</h2>
                <div class="contact-information prompt">
                    联系电话：{{ $order->shop->contact_info }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $order->shop->contact_person }}
                    &nbsp;&nbsp;&nbsp;&nbsp;地址：{{ $order->shop->address }}
                </div>
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-4">
                录单日期 ：{{ $order->created_at->toDateString() }}
            </div>
            <div class="col-xs-5">
                平台订单号 ：{{ $order->id }}
            </div>
            <div class="col-xs-3">
                单据编号 ：{{ $order->numbers }}
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-4">
                购买单位 ：{{ $order->user_shop_name }}
            </div>
            <div class="col-xs-5">
                {{ $order->shippingAddress&&$order->shippingAddress->address?$order->shippingAddress->address->address_name:'' }}
            </div>
            <div class="col-xs-3">
                制单人 ： 管理员
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table-bordered table text-center">
                    <thead>
                    <tr>
                        <th>商品条码</th>
                        <th>商品名称</th>
                        <th>商品规格</th>
                        <th>单位</th>
                        <th>数量</th>
                        <th>金额</th>
                        <th>促销信息</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->goods as $goods)
                        <tr>
                            <td>{{ $goods->bar_code }}</td>
                            <td width="250px">{{ $goods->name }}</td>
                            <td>
                                {{ $goods->{'specification_' . $order->user_type_name} }}

                            </td>
                            <td>{{ cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}</td>
                            <td>{{ $goods->pivot->num }}</td>
                            <td>{{ $goods->pivot->total_price }}</td>
                            <td>{{ $goods->promotion_info }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">总计</td>
                        <td>{{ $order->allNum }}</td>
                        <td colspan="2">{{ $order->price }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">备注：{{ $order->remark }}</td>
                        <td colspan="3">{{ $order->shop_name }}首页地址：<br/>
                            <img src="{{ (new \App\Services\ShopService())->qrcode($order->shop_id) }}"/><br/>
                            一站式零售服务平台- -订百达
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-2">制单:</div>
            <div class="col-xs-3">
                业务员：{{ $order->salesmanVisitOrder ? $order->salesmanVisitOrder->salesman_name : '' }}</div>
            <div class="col-xs-2"> 送货人：{{ $order->deliveryMan ? $order->deliveryMan->name : '' }}</div>
            <div class="col-xs-2"> 仓管：</div>
            <div class="col-xs-3">
                收款人：{{ $order->systemTradeInfo?cons()->valueLang('trade.pay_type',$order->systemTradeInfo->pay_type) : '' }}</div>
        </div>
    </div>
@stop
@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('js')
    @parent
    <script type="text/javascript" src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script>
        $(function () {
            printFun();
        });
    </script>

@stop