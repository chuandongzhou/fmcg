@extends('index.index-master')

@section('container')
    <form class="form-horizontal" action="{{ url('order/confirm-order') }}" method="post">
        {{ csrf_field() }}
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
                <form action="#" method="post">
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
                                @foreach($shop['cart_goods'] as $key=>$cartGoods)
                                    <tr>
                                        <td>
                                            <img class="avatar" src="{{ $cartGoods->goods->image_url }}">
                                            {{ $cartGoods->goods->name }}
                                        </td>
                                        <td class="text-center">￥{{ $cartGoods->goods->price }}</td>
                                        <td class="text-center">{{ $cartGoods->num }}</td>
                                        <td class="text-center"><b
                                                    class="red">￥{{ $cartGoods->goods->price *  $cartGoods->num }}</b>
                                        </td>
                                        @if($key == 0)
                                            <td class="text-center total-money"
                                                rowspan="{{ count($shop['cart_goods']) }}">
                                                合计金额 :<b class="red">￥840</b>
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
                                            <select class="control">
                                                <option>地址1地址1地址1地址1地址1地址1地址1</option>
                                            </select>
                                        </p>
                                        <p class="operating">
                                            <span>支付方式 :</span>
                                            <select>
                                                <option>在线支付</option>
                                            </select>
                                        </p>
                                        <p class="operating">
                                            <span>订单备注 :</span>
                                            <input class="control" type="text">
                                        </p>
                                    </td>
                                    <td>
                                        <button class="btn brand-cancel">管理收货地址</button>
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

    </form>
@stop

