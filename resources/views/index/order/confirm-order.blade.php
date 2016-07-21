@extends('index.index-master')

@section('subtitle' , '确认订单')

@section('container')
    @include('includes.shipping-address')
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
            <form class="form-horizontal" action="{{ url('order/submit-order') }}" method="post" autocomplete="off">
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
                                    <td class="text-center">¥{{ $cartGoods->goods->price }}</td>
                                    <td class="text-center">{{ $cartGoods->num }}</td>
                                    <td class="text-center">
                                        <b class="red">¥{{ $cartGoods->goods->price *  $cartGoods->num }}</b>
                                    </td>
                                    @if($key == 0)
                                        <td class="text-center total-money"
                                            rowspan="{{ count($shop['cart_goods']) }}">
                                            合计金额 :<b class="red">¥{{ $shop->sum_price }}</b>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <td colspan="5">
                                <p class="operating">
                                    <span>订单备注 :</span>
                                    <textarea name="shop[{{ $shop->id }}][remark]" class="control"></textarea>
                                </p>
                            </td>
                            </tfoot>
                        </table>
                    </div>

                @endforeach
                {{--<div class="col-sm-12 table-responsive shopping-table-list">--}}
                {{--<table class="table table-bordered">--}}
                {{--<tr class="address">--}}
                {{--<td colspan="4">--}}
                {{--<p class="operating">--}}
                {{--<span>收货地址 :</span>--}}
                {{--<select name="shipping_address_id" class="control">--}}
                {{--@foreach($shippingAddress as $address)--}}
                {{--<option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>--}}
                {{--{{ $address->address->address_name . '  ' . $address->consigner . '  ' .  $address->phone }}--}}
                {{--</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--</p>--}}
                {{--<p class="operating">--}}
                {{--<span>支付方式 :</span>--}}
                {{--<select name="pay_type" class="pay-type">--}}
                {{--@foreach(cons()->lang('pay_type') as $key=>$type)--}}
                {{--<option value="{{ $key }}">{{ $type }}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--</p>--}}
                {{--<p class="operating hidden pay-way">--}}
                {{--@foreach(cons()->lang('pay_way.cod') as $key=> $way)--}}
                {{--<input type="radio" {{ $key == 'cash' ? 'checked' : '' }}--}}
                {{--name="pay_way" value="{{ $key }}" disabled/>--}}
                {{--{{ $way }}  &nbsp;&nbsp;&nbsp;--}}
                {{--@endforeach--}}
                {{--</p>--}}

                {{--</td>--}}
                {{--<td>--}}
                {{--<a class="btn brand-cancel" id="add-address" href="javascript:void(0)" type="button"--}}
                {{--data-target="#shippingAddressModal"--}}
                {{--data-toggle="modal"><label><span class="fa fa-plus"></span></label>添加收货地址--}}
                {{--</a>--}}
                {{--</td>--}}
                {{--</tr>--}}
                {{--</table>--}}
                {{--</div>--}}
                <div class="col-sm-12 delivery-mode">
                    <h4 class="title">提货方式</h4>
                    <div class="row">
                        <div class="col-sm-3">
                            <a class="btn check-delivery active" href="javascript:">送货 <span
                                        class="triangle"></span><span
                                        class="fa fa-check"></span></a>
                        </div>
                        <div class="col-sm-9 table-responsive shopping-table-list">
                            <table class="table table-bordered">
                                <tr class="address">
                                    <td colspan="4">
                                        <p class="operating">
                                            <span>收货地址 :</span>
                                            <select class="control operation-buttons" name="shipping_address_id">
                                                @foreach($shippingAddress as $address)
                                                    <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                                        {{ $address->address->address_name . '  ' . $address->consigner . '  ' .  $address->phone }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </p>
                                        <p class="operating">
                                            <span>支付方式 :</span>
                                            <select class="operation-buttons pay-type" name="pay_type">
                                                @foreach(cons()->lang('pay_type') as $key=>$type)
                                                    @if($key != 'pick_up')
                                                        <option value="{{ $key }}">{{ $type }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <span class="operating hidden pay-way">
                                                @foreach(cons()->lang('pay_way.cod') as $key=> $way)
                                                    <input type="radio" {{ $key == 'cash' ? 'checked' : '' }}
                                                    name="pay_way" value="{{ $key }}" disabled
                                                           class="operation-buttons"/>
                                                    {{ $way }}  &nbsp;&nbsp;&nbsp;
                                                @endforeach
                                            </span>
                                        </p>

                                    </td>
                                    <td>
                                        <a class="btn brand-cancel operation-buttons" id="add-address"
                                           href="javascript:void(0)" type="button"
                                           data-target="#shippingAddressModal"
                                           data-toggle="modal"><span class="fa fa-plus"></span>添加收货地址
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{--<div class="row">--}}
                        {{--<div class="col-sm-12 text-left from-mentioning">--}}
                            {{--<a class="btn check-delivery from-mentioning-btn" href="javascript:">--}}
                                {{--自提 <span class="triangle"></span>--}}
                                {{--<span class="fa fa-check"></span>--}}
                            {{--</a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="col-sm-12 text-right padding-clear">
                    <a href="javascript:history.back()" class="btn btn-cancel submit-order">返回</a>
                    <button class="btn btn-primary submit-order">提交订单</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $('.pay-type').on('change', function () {
                var obj = $(this), payType = obj.val(), payWay = $('.pay-way');

                if (payType == 'cod') {
                    payWay.removeClass('hidden').children('input[type="radio"]').prop('disabled', false);
                } else {
                    payWay.addClass('hidden').children('input[type="radio"]').prop('disabled', true);
                }
            })

            $(".check-delivery").click(function () {
                var self = $(this);
                var operationButtons = $(".delivery-mode .operation-buttons");
                self.addClass("active").parents().siblings().children().find(".check-delivery").removeClass("active");
                operationButtons.prop("disabled", self.hasClass("from-mentioning-btn"));
            })

        })
    </script>
@stop

