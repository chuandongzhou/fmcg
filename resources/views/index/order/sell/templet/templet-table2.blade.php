@extends('master')
@section('css')
    @parent
    <style type="text/css" media="print">
        @page { size: landscape; }
    </style>
@stop
@section('body')

<div class="container temple-table temple-table-y">
    <div class="row">
        <div class="col-sm-12 text-center">
            <h2 class="title">{{ $order->shop->name }} 送货单</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table-bordered table text-center">
                <tr>
                    <td colspan="2" class="text-left">录单日期 ：{{ $order->created_at }}</td>
                    <td colspan="3" class="text-left">平台订单号 ： {{ $order->id }}</td>
                    <td colspan="2" class="text-left">单据编号 ：{{ $order->numbers }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-left">
                        购买单位 {{ $order->user_shop_name }} &nbsp;&nbsp;&nbsp;&nbsp;联系电话 ：{{ $order->shop->contact_person }}-{{ $order->shop->contact_info }}&nbsp;&nbsp;&nbsp;&nbsp;地址 ：{{ $order->shop->address }}
                    </td>
                </tr>
                <tr>
                    <th>商品条码</th>
                    <th width="200px">商品名称</th>
                    <th width="80px">商品规格</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>金额</th>
                    <th>促销信息</th>
                </tr>
                <tr>
                    <td>36985274123654789</td>
                    <td>商品</td>
                    <td>1000ml</td>
                    <td>个</td>
                    <td>2</td>
                    <td>2.00</td>
                    <td>暂无</td>
                </tr>
                @foreach($order->goods as $goods)
                <tr>
                    <td>{{ $goods->bar_code }}</td>
                    <td >{{ $goods->name }}</td>
                    <td>
                        {{ $goods->{'specification_' . $order->user_type_name} }}

                    </td>
                    <td>{{ $goods->pivot->pieces }}</td>
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
                    <td colspan="3">{{ $order->shop_name }}首页地址：<br />
                        <img src="{{ (new \App\Services\ShopService())->qrcode($order->shop_id) }}" /><br/>
                        一站式零售服务平台- -订百达</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row item">
        <div class="col-xs-4">业务员：{{ $order->salesmanVisitOrder ? $order->salesmanVisitOrder->salesman_name : '' }}</div>
        <div class="col-xs-4"> 送货人：{{ $order->deliveryMan ? $order->deliveryMan->name : '' }}</div>
        <div class="col-xs-4">  收款人：{{ $order->systemTradeInfo?cons()->valueLang('trade.pay_type',$order->systemTradeInfo->pay_type) : '' }}</div>
    </div>
</div>
@stop
