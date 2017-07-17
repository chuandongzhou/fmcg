@extends('index.index-master')

@section('subtitle' , '确认订单')

@section('container')
    @include('includes.shipping-address')
    <div class="container-wrap">
        <div class="container dealer-index index">
            <div class="row audit-step-outs">
                <div class="col-sm-3 step ">
                    1.查看购物车
                    <span></span>
                    <span class="triangle-right first"></span>
                    <span class="triangle-right last"></span>
                </div>
                <div class="col-sm-3 step step-active">
                    2.确认订单
                    <span class="triangle-right first"></span>
                    <span class="triangle-right last"></span>
                </div>
                <div class="col-sm-3 step">
                    3.提交订单
                    <span class="triangle-right first"></span>
                    <span class="triangle-right last"></span>
                </div>
                <div class="col-sm-3 step">
                    4.等待确认
                </div>
            </div>
            <div class="row table-list-row confirm-order ">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/order/submit-order') }}"
                      data-done-url="{{ url('order/finish-order') }}" method="post" autocomplete="off">

                    <div class="col-sm-12 delivery-mode delivery-option">
                        <h4 class="title">提货方式</h4>
                        <div class="item checks delivery-way-list">
                            <a class="btn check-item from-mentioning-btn pick-up">自提
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </a>
                            <a class="btn check-item  active delivery-btn">送货
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-12 delivery-item delivery-option">
                        <h4 class="title msg-title">收货信息</h4>
                        <div class="item clearfix">
                            <div class="pull-left address-item option-panel">
                                @if(!$shippingAddress->isEmpty())
                                    <select class="form-control" name="shipping_address_id">
                                        @foreach($shippingAddress as $address)
                                            <option value="{{ $address->id }}">
                                                {{$address->consigner .' '. $address->phone .' '. $address->address->address_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div>
                                        无收货地址，请添加
                                    </div>
                                @endif
                            </div>
                            <a class=" add" id="add-address" href="javascript:void(0)" type="button"
                               data-target="#shippingAddressModal" data-toggle="modal">
                                <label>
                                    <span class="fa fa-plus"></span>
                                </label>新增收货地址
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-12 delivery-item delivery-option">
                        <h4 class="title">支付方式</h4>
                        <div class=" item clearfix pay-type-list">
                            <div class="pull-left">
                                <a class="btn check-item online-pay active pay-type">在线支付
                                    <span class="triangle"></span>
                                    <span class="fa fa-check"></span>
                                    <input type="radio" name="pay_type" class="hidden" value="online" checked/>
                                </a>
                            </div>
                            <div class="cash-on-delivery  pull-left">
                                <div class="btn check-item option-panel pay-type">
                                    <input type="hidden" value="cash" name="pay_way"/>
                                    <input type="radio" name="pay_type" class="hidden" value="cod">
                                    <div class="default-checked">货到付款 :
                                        <span class="content">现金</span>
                                        <span class="fa fa-angle-down"></span>
                                    </div>
                                    <span class="triangle"></span>
                                    <span class="fa fa-check"></span>
                                    <div class="option-wrap cash-on-item text-center ">
                                        <div class="option-item pay_way_select" data-value="cash">现金</div>
                                        <div class="option-item pay_way_select" data-value="card">刷卡</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 table-responsive shopping-table-list delivery-option">
                        <h4 class="title">商品清单</h4>
                        @foreach($shops as $shop)
                            <table class="table table-bordered shop-item" data-id="{{ $shop->id }}">
                                <tbody>
                                <tr>
                                    <th colspan="3">{{ $shop->name }}
                                        ({{ cons()->valueLang('user.type' , $shop->user->type) }})
                                    </th>
                                </tr>
                                @foreach($shop['cart_goods'] as $key => $cartGoods)
                                    <tr class="goods-item" data-id="{{ $cartGoods->goods->id }}">
                                        <td class="store-name">
                                            <img class="avatar" src="{{ $cartGoods->goods->image_url }}">
                                            {{ $cartGoods->goods->name }}
                                        </td>
                                        <td class="text-left unit-price">¥ <span
                                                    class="goods-price-{{ $cartGoods->goods_id }} goods-price"
                                                    data-price="{{ $cartGoods->goods->price }}">{{ $cartGoods->goods->price }}</span>{{ '/'.$cartGoods->goods->pieces }}
                                        </td>
                                        <td class="text-left">x <span class="goods-num"
                                                                      data-num="{{ $cartGoods->num }}">{{ $cartGoods->num }}</span>
                                        </td>
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
                                    </td>
                                </tr>
                                @if(!$shop->coupons->isEmpty())
                                    <tr>
                                        <td colspan="3">
                                            <div class="operating">
                                                <span class="prompt">优惠券 :</span>
                                                <select class="control-select coupon-control"
                                                        name="shop[{{ $shop->id }}][coupon_id]">
                                                    @foreach($shop->coupons as $coupon)
                                                        <option value="{{ $coupon->id }}"
                                                                data-discount="{{ $coupon->discount }}"
                                                                data-full="{{ $coupon->full }}"
                                                        >
                                                            满 {{ $coupon->full }}
                                                            减 {{ $coupon->discount }}</option>
                                                    @endforeach
                                                    <option value="0" data-discount="0">不使用优惠券</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3">
                                        <div class="count-panel">
                                            <p class="count min-money">
                                            <span class="prompt min-money-span" data-money="{{ $shop->min_money  }}">
                                                <span class="full">满足最低配送额</span>
                                                <span class="not-full">不满足最低配送额</span>
                                                ¥<span class="shop-min-money shop-min-money-{{ $shop->id }}"></span>
                                            </span>
                                                <span class="name">合计 :&nbsp;</span>
                                                <span class="red shop-sum-price"
                                                      data-price="{{ $shop->sum_price }}">{{ $shop->sum_price }}</span>

                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        @endforeach

                    </div>
                    <div class="col-sm-12 padding-clear ">
                        <div class="count-panel finally">
                            <p class="count">
                                <span class="name"><b
                                            class="red">{{ $shops->pluck('cart_goods')->collapse()->count() }}</b>件商品&nbsp;总商品金额 :&nbsp;</span>
                                <span class="red">¥ <b class="sum-price"
                                                       data-sum-price="{{ $shops->sum('sum_price') }}">{{ number_format($shops->sum('sum_price'), 2) }}</b></span>
                            </p>
                            <p class="count">
                                <span class="name">优惠券 :&nbsp;</span>
                                <span class="red">-¥<b class="sum-discount">0</b></span>
                            </p>
                            <p class="count">
                                <span class="name">应付金额 :&nbsp;</span>
                                <span class="red">¥ <b class="amount"></b></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-12 text-right padding-clear">
                        <button type="submit" class="btn btn-primary submit-order">提交订单</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        var address = $('.address-select'),
            submitOrderControl = $('.submit-order');

        var Confirm = function () {
            this.pickUp = false;
        };

        Confirm.prototype = {
            //优惠券选择
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
                        shopSumPrice = parseFloat(shopSumPriceControl.data('price'));
                    if (discount) {
                        sumDiscount = sumDiscount.add(discount);
                        shopSumPriceControl.html((shopSumPrice.sub(discount)) + '(' + shopSumPrice + '-' + discount + ')');
                    } else {
                        shopSumPriceControl.html(shopSumPrice.toFixed(2));
                    }
                });
                if (sumDiscount > 0) {
                    sumDiscountControl.html(sumDiscount.toFixed(2)).parents('.count').show();

                } else {
                    sumDiscountControl.html(sumDiscount).parents('.count').hide();
                }
                amountControl.html(parseFloat(parseFloat(sumPriceControl.data('sumPrice')).sub(sumDiscount)).toFixed(2));
                this.overThanShopMinMoney();
            },
            //店铺最低配送额
            shopMinMoney: function () {
                var that = this, shopItem = $('.shop-item'), shopIds = [],
                    shippingAddressId = $('select[name="shipping_address_id"]').val();
                shopItem.each(function () {
                    shopIds.push($(this).data('id'));
                });
                $.ajax({
                    url: site.api('order/min-money'),
                    method: 'get',
                    data: {shipping_address_id: shippingAddressId, shop_id: shopIds}
                }).done(function (data, textStatus, jqXHR) {
                    var shopMinMoney = data.shopMinMoney;

                    for (var i in shopMinMoney) {
                        var minMoney = shopMinMoney[i]['min_money'];
                        $('.shop-min-money-' + shopMinMoney[i]['shop_id']).data('min-money', minMoney).html(minMoney);
                    }

                }).fail(function (jqXHR, textStatus, errorThrown) {


                }).always(function (data, textStatus, jqXHR) {
                    that.overThanShopMinMoney();
                });
            },
            //提货方式
            deliveryMode: function (deliveryMode) {
                var that = this, goodsItem = $('.goods-item'), goodsIds = [], deliveryMode = deliveryMode || 1;  //deliveryMode: 1送货 2自提
                goodsItem.each(function () {
                    goodsIds.push($(this).data('id'));
                });
                $.ajax({
                    url: site.api('order/goods-price'),
                    method: 'get',
                    data: {delivery_mode: deliveryMode, goods_id: goodsIds}
                }).done(function (data, textStatus, jqXHR) {
                    var goodsPrice = data.goodsPrice;

                    for (var i in goodsPrice) {
                        $('.goods-price-' + goodsPrice[i]['goods_id']).data('price', goodsPrice[i]['price']).html(goodsPrice[i]['price']);
                    }
                    that.formatShopSumPrice();
                    that.formatSumPrice();
                    that.formatCouponControl();
                    that.discountAmount();
                    that.overThanShopMinMoney();
                }).fail(function (jqXHR, textStatus, errorThrown) {


                }).always(function (data, textStatus, jqXHR) {

                });
            },
            //店铺总价格
            formatShopSumPrice: function () {
                $('.shop-item').each(function () {
                    var obj = $(this), sumPrice = 0, sumPricePanel = obj.find('.shop-sum-price'),
                        goodsItem = obj.find('.goods-item');
                    goodsItem.each(function () {
                        var self = $(this),
                            goodsPrice = parseFloat(self.find('.goods-price').data('price')),
                            goodsNum = self.find('.goods-num').data('num');
                        sumPrice = sumPrice.add(goodsPrice.mul(goodsNum));
                    });
                    sumPricePanel.data('price', sumPrice).html(sumPrice.toFixed(2));
                })
            },
            //是否超过最低配送额
            overThanShopMinMoney: function () {
                submitOrderControl.prop('disabled', false);
                if (!this.pickUp) {
                    $('.shop-item').each(function () {
                        var obj = $(this),
                            sumPricePanel = obj.find('.shop-sum-price'),
                            sumPrice = parseFloat(sumPricePanel.data('price')),
                            shopMinMoneyPanel = obj.find('.shop-min-money'),
                            minMoney = parseFloat(shopMinMoneyPanel.data('minMoney')),
                            minMoneySpan = obj.find('.min-money-span');
                        if (minMoney > sumPrice) {
                            submitOrderControl.prop('disabled', true);
                            minMoneySpan.addClass('red').children('.not-full').removeClass('hide').siblings('.full').addClass('hide');
                        } else {
                            minMoneySpan.removeClass('red').children('.not-full').addClass('hide').siblings('.full').removeClass('hide');
                        }
                    })
                }
            },
            //订单总价格
            formatSumPrice: function () {
                var sumPricePanel = $('.sum-price'), sumPrice = 0;
                $('.shop-item').each(function () {
                    var shopSumPrice = $(this).find('.shop-sum-price').data('price');
                    sumPrice = sumPrice.add(parseFloat(shopSumPrice));
                });

                sumPricePanel.data('sum-price', sumPrice).html(sumPrice.toFixed(2));
            },
            //格式化优惠券
            formatCouponControl: function () {
                $('.shop-item').each(function () {
                    var obj = $(this),
                        shopSumPrice = obj.find('.shop-sum-price').data('price'),
                        couponControl = obj.find('.coupon-control');
                    if (couponControl.length) {
                        var couponId = 0;
                        couponControl.children('option').each(function () {
                            var self = $(this), full = self.data('full');
                            if ($(this).data('discount') > 0) {
                                if (full > shopSumPrice) {
                                    self.prop('disabled', true);
                                }
                                else {
                                    couponId = couponId || self.attr('value');
                                    self.prop('disabled', false);
                                }
                            }
                        });
                        couponControl.val(couponId);
                    }

                });
            },
            //设置是否为自提
            setPickUp: function (status) {
                this.pickUp = status;
            }
        };

        var confirms = new Confirm();


        $('select.coupon-control').on('change', function () {
            confirms.discountAmount();
        });

        $('select[name="shipping_address_id"]').on('change', function () {
            confirms.shopMinMoney();
        });
        confirms.discountAmount();
        confirms.shopMinMoney();

        //配送方式选择
        $('.delivery-way-list').children('.check-item').on('click', function () {
            var obj = $(this), deliveryItem = $(".delivery-item");

            if (obj.hasClass('pick-up')) {
                confirms.setPickUp(true);
                deliveryItem.hide();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', true);
                });
                confirms.deliveryMode({{ cons('order.delivery_mode.pick_up') }});
                $('.min-money-span').hide();
                $('.submit-order').prop('disabled', false).removeClass('btn-cancel').addClass('btn-primary');
            } else {
                confirms.setPickUp(false);
                deliveryItem.show();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', false);
                });
                confirms.deliveryMode({{ cons('order.delivery_mode.delivery') }});
                $('.min-money').show();
                $('.min-money-span').show();
            }
            obj.addClass('active').siblings().removeClass("active");
        });

        $('.pay-type-list  .pay-type').on('click', function () {
            var self = $(this);
            self.find('input[name="pay_type"]').prop('checked', true);
            self.addClass("active").parents().siblings().children().removeClass("active");
        });

        $("div.default-checked").on('click', function (e) {
            $(this).siblings(".option-wrap").slideToggle();
        })

        $(".option-item").click(function () {
            var self = $(this), checkedHtml = self.html(), optionWrap = self.parents(".option-wrap");

            $('input[name="pay_way"]').val(self.data('value'));
            optionWrap.siblings(".default-checked").children(".content").html(checkedHtml);
            optionWrap.slideUp();
        })

        $('.option-panel').click(function (e) {
            e.stopPropagation();
        });
    </script>
@stop

