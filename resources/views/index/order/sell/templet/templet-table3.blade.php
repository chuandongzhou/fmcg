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
                购买单位 ：{{ $order->user_shop_name .'-'. $order->user_contact_info }}
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
                <table class="table-bordered table-middle table text-center">
                    <thead>
                    <tr>
                        <th>商品条码</th>
                        <th>商品名称</th>
                        <th>商品规格</th>
                        <th>单价</th>
                        <th>单位</th>
                        <th>数量</th>
                        <th>金额</th>
                        <th>促销信息</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orderGoods as $goods)
                        <tr>
                            <td width="150px">{{ $goods->bar_code }}</td>
                            <td width="250px">{{ $goods->name }}</td>
                            <td width="100px">
                                {{ $goods->{'specification_' . $order->user_type_name} }}

                            </td>
                            <td width="80px">{{ cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}</td>
                            <td width="80px">{{$goods->pivot->price}}</td>
                            <td width="80px">{{ $goods->pivot->num }}</td>
                            <td width="80px">{{ $goods->pivot->total_price }}</td>
                            <td>{{ $goods->promotion_info }}</td>
                        </tr>
                    @endforeach
                    @foreach($order->gifts as $gift)
                        <tr>
                            <td>{{ $gift->bar_code }}</td>
                            <td>{{ $gift->name }}</td>
                            <td>
                                {{ $gift->{'specification_' . $order->user_type_name} }}

                            </td>
                            <td>{{$gift->pivot->price}}</td>
                            <td>{{ cons()->valueLang('goods.pieces', $gift->pivot->pieces) }}</td>
                            <td>{{ $gift->pivot->num }}</td>
                            <td>{{ $gift->pivot->total_price }}</td>
                            <td>赠品</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5">总计</td>
                        <td>{{ $order->allNum }}</td>
                        <td colspan="2">
                            {{ $order->price }}
                            @if($order->coupon_id && $order->coupon)
                                - {{ bcsub($order->price,$order->after_rebates_price,2 ) }}
                                = {{ $order->after_rebates_price }}
                            @elseif($order->display_fee > 0)
                                - {{ $order->display_fee }}
                                = {{ $order->after_rebates_price }}
                            @endif
                        </td>
                    </tr>
                    @if(!$mortgageGoods->isEmpty() || $order->display_fee > 0)
                        <tr>
                            <td align="center" colspan="8">陈列费</td>
                        </tr>
                        @foreach($mortgageGoods as $goods)
                            <tr>
                                <td>{{ $goods->bar_code }}</td>
                                <td width="250px">{{ $goods->name }}</td>
                                <td>
                                    {{ $goods->{'specification_' . $order->user_type_name} }}
                                </td>
                                <td colspan="2">{{ $goods->pivot->num.cons()->valueLang('goods.pieces', $goods->pivot->pieces) }}</td>
                                @if ($goods == $mortgageGoods->first())
                                    <td colspan="3"
                                        rowspan="{{ $order->display_fee > 0 ? $mortgageGoods->count() + 1 : $mortgageGoods->count() }}">{{ $order->remarkGroup['display'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                        @if($order->display_fee > 0)
                            <tr>
                                <td></td>
                                <td width="250px">现金</td>
                                <td colspan="3">{{ $order->display_fee }}</td>
                                @if($mortgageGoods->isEmpty())
                                    <td colspan="3">{{ $order->remarkGroup['display'] }}</td>
                                @endif
                            </tr>
                        @endif
                    @endif
                    @if($order->promo)
                        <tr>
                            <td align="center" colspan="8">促销活动</td>
                        </tr>
                        @if(in_array($order->promo->type,[cons('promo.type.goods-goods'),cons('promo.type.money-goods')]))
                            @foreach($order->promo->rebate as $rebate)
                                <tr>
                                    @if($rebate == $order->promo->rebate->first())
                                        <td colspan="3"
                                            rowspan="{{$order->promo->rebate->count()}}">{{$order->promo->name}}</td>
                                    @endif
                                    <td colspan="2">{{$rebate->goods->name ?? ''}}</td>
                                    <td colspan="3">{{$rebate->quantity . cons()->valueLang('goods.pieces',$rebate->unit)}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">{{$order->promo->name}}</td>
                                <td colspan="6">
                                                    <span>
                                                        {{'('.($order->promo->type == cons('promo.type.custom') ? '自定义' : '现金').')'}}
                                                    </span>
                                    @if(in_array($order->promo->type,[cons('promo.type.goods-money'),cons('promo.type.money-money')]))
                                        {{$order->promo->rebate[0]->money}} 元
                                    @else
                                        {{$order->promo->rebate[0]->custom ?? ''}}
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        @endif
                    @endif
                    <tr>
                        <td colspan="5" class="especially">备注：{{ $order->remarkGroup['remark'] }}</td>
                        <td colspan="3">{{ $order->shop_name }}首页地址：<br/>
                            <img src="{{ (new \App\Services\ShopService())->qrcode($order->shop_id,80) }}"/><br/>
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
            <div class="col-xs-2">送货人：
                @foreach($order->deliveryMan as $deliveryMan)
                    {{ $deliveryMan ? $deliveryMan->name : '' }}&nbsp;&nbsp;&nbsp;
                @endforeach
            </div>
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