@extends('index.index-master')

@section('container')
    <div class="container dealer-index index">
        <div class="row audit-step-outs">
            <div class="col-sm-3 step ">
                1.查看购物车
                <span></span>
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step step-active">
                2.确认订单消息
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step">
                3.成功提交订单
                <span class="triangle-right first"></span>
                <span class="triangle-right last"></span>
            </div>
            <div class="col-sm-3 step">
                4.等待确认
            </div>
        </div>
        <div class="row table-list-row">
            <form class="form-horizontal" action="{{ url('order/submit-order') }}" method="post">
                {{ csrf_field() }}
                @foreach($shops as $shop)
                    <div class="col-sm-12 table-responsive shopping-table-list">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>
                                    {{ $shop->name }}
                                </th>
                                <th class="text-center">商品单价</th>
                                <th class="text-center">商品数量</th>
                                <th class="text-center">金额</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shop['cart_goods'] as $key => $cartGoods)
                                <tr>
                                    <td>
                                        <img class="avatar" src="{{ $cartGoods->goods->image_url }}">
                                        {{ $cartGoods->goods->name }}
                                    </td>
                                    <td class="text-center">￥{{ $cartGoods->goods->price }}</td>
                                    <td class="text-center">{{ $cartGoods->num }}</td>
                                    <td class="text-center">
                                        <b class="red">￥{{ $cartGoods->goods->price *  $cartGoods->num }}</b>
                                    </td>
                                    @if($key == 0)
                                        <td class="text-center total-money"
                                            rowspan="{{ count($shop['cart_goods']) }}">
                                            合计金额 :<b class="red">￥{{ $shop->sum_price }}</b>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4">
                                    <p class="operating">
                                        <span>收货地址 :</span>
                                        <select name="shop[{{ $shop->id }}][shipping_address_id]" class="control">
                                            @foreach($shippingAddress as $address)
                                                <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                                    {{ $address->address . '  ' . $address->consigner . '  ' .  $address->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p class="operating">
                                        <span>支付方式 :</span>
                                        <select name="shop[{{ $shop->id }}][pay_type]">
                                            @foreach(cons('pay_type') as $type)
                                                <option value="{{ $type }}">{{ cons()->valueLang('pay_type' , $type) }}</option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p class="operating">
                                        <span>订单备注 :</span>
                                        <input name="shop[{{ $shop->id }}][remark]" class="control" type="text">
                                    </p>
                                </td>
                                <td>
                                    <a class="btn brand-cancel">管理收货地址</a>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                @endforeach
                <div class="col-sm-12 text-right padding-clear">
                    <button class="btn btn-primary submit-order">提交订单</button>
                </div>
            </form>
        </div>
    </div>

@stop

