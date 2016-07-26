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
        <div class="row table-list-row confirm-order ">
            <form class="form-horizontal" action="{{ url('order/submit-order') }}" method="post" autocomplete="off">
                {{ csrf_field() }}
                <div class="col-sm-12 delivery-mode">
                    <h4 class="title">提货方式</h4>
                    <div class="item checks delivery-way-list">
                        <a class="btn check-item from-mentioning-btn pick-up">自提
                            <span class="triangle"></span>
                            <span class="fa fa-check"></span>
                        </a>
                        <a class="btn check-item  active delivery">送货
                            <span class="triangle"></span>
                            <span class="fa fa-check"></span>
                        </a>
                    </div>
                </div>
                <div class="col-sm-12 delivery-item">
                    <h4 class="title msg-title">收货信息</h4>
                    <div class="item  address-item">
                        <a class="pull-right" id="add-address" href="javascript:void(0)" type="button"
                           data-target="#shippingAddressModal" data-toggle="modal">
                            <label>
                                <span class="fa fa-plus"></span>
                            </label>新增收货地址
                        </a>
                        <select class="control-select operation-buttons" name="shipping_address_id">
                            @foreach($shippingAddress as $address)
                                <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                    {{ $address->address->address_name . '  ' . $address->consigner . '  ' .  $address->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 delivery-item">
                    <h4 class="title">支付方式</h4>

                    <div class=" item clearfix pay-type-list">
                        <div class="pull-left">
                            <a class="btn check-item online-pay active pay-type">在线支付
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                                <input type="radio" name="pay_type" class="hidden" value="online" checked>
                            </a>
                        </div>
                        <div class="cash-on-delivery pull-left">
                            <a class="btn check-item pay-type">货到付款
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                                <input type="radio" name="pay_type" class="hidden" value="cod">
                            </a>
                            <div class="item cash-on-item pay-way">
                                <a class="btn check-item cash">现金
                                    <span class="triangle"></span>
                                    <span class="fa fa-check"></span>
                                    <input type="radio" name="pay_way" class="hidden" value="cash" disabled>
                                </a>
                                <a class="btn check-item card active">刷卡
                                    <span class="triangle"></span>
                                    <span class="fa fa-check"></span>
                                    <input type="radio" name="pay_way" class="hidden" value="card" disabled checked>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 table-responsive shopping-table-list">
                    <h4 class="title">商品清单 : </h4>

                    @foreach($shops as $shop)
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="3" class="shop-item" data-id="{{ $shop->id }}">
                                    商家 : {{ $shop->name }}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shop['cart_goods'] as $key => $cartGoods)
                                <tr>
                                    <td class="store-name">
                                        <img class="avatar" src="{{ $cartGoods->goods->image_url }}">
                                        {{ $cartGoods->goods->name }}
                                    </td>
                                    <td class="text-center unit-price">¥ {{ $cartGoods->goods->price }}</td>
                                    <td class="text-center">x {{ $cartGoods->num }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3">
                                    <div class="operating">
                                        <span class="prompt">添加订单备注 :</span>
                                        <input name="shop[{{ $shop->id }}][remark]" class="control" type="text"/>
                                    </div>
                                    @if(!$shop->coupons->isEmpty())
                                        <div class="operating">
                                            <span class="prompt">优惠券 :</span>
                                            <select class="control-select coupon-control"
                                                    name="shop[{{ $shop->id }}][coupon_id]">
                                                @foreach($shop->coupons as $coupon)
                                                    <option value="{{ $coupon->id }}"
                                                            data-discount="{{ $coupon->discount }}">
                                                        满 {{ $coupon->full }}
                                                        减 {{ $coupon->discount }}</option>
                                                @endforeach
                                                <option value="" data-discount="0">不使用优惠券</option>
                                            </select>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="count-panel">
                                        <p class="count min-money">
                                            <span class="name">最低配送额 :&nbsp;</span>
                                            <b class="red {{ 'shop-min-money-' . $shop->id }}">{{ $shop->min_money }}</b>
                                        </p>
                                        <p class="count"><span class="name">合计 :&nbsp;</span>
                                            <b class="red shop-sum-price">{{ $shop->sum_price }} {{--90(100-10)--}}</b>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    @endforeach
                </div>
                <div class="col-sm-12 padding-clear">
                    <div class="count-panel finally">
                        <p class="count">
                            <span class="name">
                                <b class="red">
                                    {{ $shops->pluck('cart_goods')->collapse()->count() }}
                                </b>件商品&nbsp;总商品金额 :&nbsp;
                            </span>
                            <span class="red">
                                ¥ <b class="sum-price">{{ $shops->sum('sum_price') }}</b>
                            </span>
                        </p>
                        <p class="count">
                            <span class="name">优惠券 :&nbsp;</span>
                            <span class="red">-¥<b class="sum-discount">0</b></span>
                        </p>
                        <p class="count">
                            <span class="name">应付金额 :&nbsp;</span>
                            <span class="red">¥ <b class="amount"></b></span></p>
                    </div>
                </div>
                <div class="col-sm-12 text-right padding-clear">
                    <button class="btn btn-cancel submit-order">取消</button>
                    <button class="btn btn-primary submit-order">提交订单</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    @parent
    <script type="text/javascript">
        var deliveryWayList = $('.delivery-way-list'),
                payTypeList = $('.pay-type-list'),
                payWay = $('.pay-way');

        deliveryWayList.children('.check-item').on('click', function () {
            var obj = $(this), deliveryItem = $(".delivery-item");

            if (obj.hasClass('pick-up')) {
                deliveryItem.hide();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', true);
                });
                $('.min-money').hide();
            } else {
                deliveryItem.show();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', false);
                });
                $('.min-money').show();
            }
            obj.addClass('active').siblings().removeClass("active");
        });
        payTypeList.find('.pay-type').on('click', function () {
            var self = $(this);
            if (self.hasClass("online-pay")) {
                $(".cash-on-item").hide().find('input[name="pay_way"]').prop('disabled', true);
            } else if (self.siblings().hasClass("cash-on-item")) {
                $(".cash-on-item").show().find('input[name="pay_way"]').prop('disabled', false);
            }
            self.find('input[name="pay_type"]').prop('checked', true);

            self.addClass("active").parents().siblings().children().removeClass("active");
        });
        payWay.children('.check-item').on('click', function () {
            var obj = $(this);

            obj.children('input[name="pay_way"]').prop('checked', true);
            obj.addClass('active').siblings().removeClass("active");
        });

        var confirmFunc = {

            discountAmount: function () {
                var discountControl = $('select.coupon-control'),
                        sumDiscount = 0,
                        sumPriceControl = $('.sum-price'),
                        sumDiscountControl = $('.sum-discount'),
                        amountControl = $('.amount');
                discountControl.each(function () {
                    var obj = $(this),
                            discount = parseFloat(obj.children('option:selected').data('discount')),
                            shopSumPriceControl = obj.closest('table').find('.shop-sum-price'),
                            shopSumPrice = parseFloat(shopSumPriceControl.html());

                    if (discount) {
                        sumDiscount = sumDiscount.add(discount);
                        shopSumPriceControl.html((shopSumPrice.add(discount).toFixed(2)) + '(' + shopSumPrice + '-' + discount + ')');
                    } else {
                        shopSumPriceControl.html(shopSumPrice.toFixed(2));
                    }

                });

                if (sumDiscount > 0) {
                    sumDiscountControl.html(sumDiscount.toFixed(2)).parents('.count').show();

                } else {
                    sumDiscountControl.html(sumDiscount).parents('.count').hide();
                }
                amountControl.html(parseFloat(sumPriceControl.html()) - sumDiscount);
            },
            shopMinMoney: function () {
                var shopItem = $('.shop-item'), shopIds = [], shippingAddressId = $('select[name="shipping_address_id"]').val();
                shopItem.each(function () {
                    shopIds.push($(this).data('id'));
                });
                $.ajax({
                    url: site.api('shop/min-money'),
                    method: 'get',
                    data: {shipping_address_id: shippingAddressId, shop_id: shopIds}
                }).done(function (data, textStatus, jqXHR) {
                    var shopMinMoney = data.shopMinMoney;

                    for (var i in shopMinMoney) {
                        $('.shop-min-money-' + i).html(shopMinMoney[i]);
                    }

                }).fail(function (jqXHR, textStatus, errorThrown) {


                }).always(function (data, textStatus, jqXHR) {

                });
            }

        };

        $('select.coupon-control').on('change', function () {
            confirmFunc.discountAmount();
        });

        $('select[name="shipping_address_id"]').on('change', function () {
            confirmFunc.shopMinMoney();
        });
        confirmFunc.discountAmount();
        confirmFunc.shopMinMoney();
    </script>
@stop

