@extends('master')
@section('title', '订单打印 | 订百达 - 订货首选')
@section('body')
    @yield('container')
    <div class="container temple-table temple-table-y">
        <div class="row">
            <div class="col-xs-12 text-center">
                <h2 class="title">{{ $order->salesman->shop->name }} 退货单</h2>
                <div class="contact-information prompt">
                    联系电话：{{ $order->salesman->shop->contact_info }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $order->salesman->shop->contact_person }}
                    &nbsp;&nbsp;&nbsp;&nbsp;地址：{{ $order->salesman->shop->address }}
                </div>
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-4">
                录单日期 ：{{ $order->created_at->toDateString() }}
            </div>
            <div class="col-xs-5">

            </div>
            <div class="col-xs-3">
                退货单号 ：{{ $order->id }}
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-4">
                退货单位 ：{{ $order->salesmanCustomer->name }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $order->salesmanCustomer->contact .'-'. $order->salesmanCustomer->contact_information }}
            </div>
            <div class="col-xs-5">
                {{ $order->salesmanCustomer->shipping_address_name }}
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
                        <th>数量</th>
                        <th>估价</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orderGoods as $goods)
                        <tr>
                            <td width="150px">{{ $goods->goods->bar_code }}</td>
                            <td width="250px">{{ $goods->goods_name }}</td>
                            <td width="100px">
                                {{ $goods->goods->{'specification_' . $order->user_type_name} }}
                            </td>
                            <td width="80px">{{ $goods->num }}</td>
                            <td width="80px">{{ $goods->amount }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">总计</td>
                        <td colspan="1">
                            {{ $order->amount }}
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="2">{{ $order->salesman->shop->name }}首页地址：<br/>
                            <img src="{{ (new \App\Services\ShopService())->qrcode($order->salesman->shop_id,80) }}"/><br/>
                            一站式零售服务平台- -订百达
                        </td>
                        <td colspan="4" height="16%">
                            处理结果
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row item">
            <div class="col-xs-4">客户签名:</div>
            <div class="col-xs-4">接收人签名：</div>
            <div class="col-xs-4">仓管签名：</div>
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