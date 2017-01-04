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
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/order/submit-order') }}" data-done-url="{{ url('order/finish-order') }}" method="post" autocomplete="off">

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
                                    <input class="shipping_address_inp" name="shipping_address_id" type="hidden"
                                           value="{{  !isset($shippingAddress->where('is_default',1)[0])? $shippingAddress[0]->id: $shippingAddress->where('is_default',1)[0]->id }}"/>
                                    <div class="default-checked" id="default-checked">
                                        <div class="content">
                                            <span>{{ !isset($shippingAddress->where('is_default',1)[0])? $shippingAddress[0]->consigner:$shippingAddress->where('is_default',1)[0]->consigner }}</span>
                                            <span class="tel-num">{{ !isset($shippingAddress->where('is_default',1)[0])? $shippingAddress[0]->phone:$shippingAddress->where('is_default',1)[0]->phone }}</span>
                                            <div class="address">{{ !isset($shippingAddress->where('is_default',1)[0])? $shippingAddress[0]->address->address_name:$shippingAddress->where('is_default',1)[0]->address->address_name }}</div>
                                        </div>
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                    <div class="option-wrap">
                                        @foreach($shippingAddress as $address)
                                            <div class="option-item address-select" data-id="{{ $address->id }}">
                                                <span> {{$address->consigner }}</span>
                                                <span class="tel-num">{{ $address->phone }}</span>
                                                <span class="address">{{ $address->address->address_name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
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
                        <h4 class="title">商品清单 : </h4>
                        @foreach($shops as $shop)
                            <table class="table table-bordered shop-item">
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
                                                    class="goods-price-{{ $cartGoods->goods->id }} goods-price"
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
                                <tr>
                                    <td colspan="3">
                                        @if(!$shop->coupons->isEmpty())
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
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">

                                        <div class="count-panel">
                                            <p class="count min-money">
                                            <span class="prompt min-money-span {{ $shop->sum_price>=$shop->min_money?'':'red' }}"
                                                  data-money="{{ $shop->min_money  }}">
                                               ({{ $shop->sum_price>=$shop->min_money?'满足最低配送额¥'.$shop->min_money:'不满足最低配送额￥'.$shop->min_money }}
                                                )
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
                             <span class="name">
                                <b class="red">
                                    {{ $shops->pluck('cart_goods')->collapse()->count() }}
                                </b>件商品&nbsp;总商品金额 :&nbsp;
                                 </span> <b class="sum-price red" data-sum-price="{{ $shops->sum('sum_price') }}">
                                    ¥ {{ $shops->sum('sum_price') }}
                                </b>
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
        var deliveryWayList = $('.delivery-way-list'),
                payTypeList = $('.pay-type-list'),
                payWay = $('.pay-way'),
                address = $('.address-select');

        //不是自提时，检查是否所有店铺都满足最低配送额
        function checkeSubmitBtn() {
            $('.min-money-span').each(function () {
                if ($(this).hasClass('red')) {
                    $('.submit-order').prop('disabled', true).removeClass('btn-primary').addClass('btn-cancel');
                }
            });
        }
        checkeSubmitBtn();

        deliveryWayList.children('.check-item').on('click', function () {
            var obj = $(this), deliveryItem = $(".delivery-item");

            if (obj.hasClass('pick-up')) {
                deliveryItem.hide();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', true);
                });
                confirmFunc.deliveryMode({{ cons('order.delivery_mode.pick_up') }});
                $('.min-money').hide();
                $('.submit-order').prop('disabled', false).removeClass('btn-cancel').addClass('btn-primary');
            } else {
                deliveryItem.show();
                deliveryItem.each(function () {
                    var self = $(this);
                    self.find('select,input').prop('disabled', false);
                });
                confirmFunc.deliveryMode({{ cons('order.delivery_mode.delivery') }});
                $('.min-money').show();
                checkeSubmitBtn();
            }
            obj.addClass('active').siblings().removeClass("active");
        });
        payTypeList.find('.pay-type').on('click', function () {
            var self = $(this);
            self.find('input[name="pay_type"]').attr('checked', 'checked');
            self.parent().siblings().find('input[name="pay_type"]').removeAttr('checked');
            self.addClass("active").parents().siblings().children().removeClass("active");
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
                            shopMinMoneySpan = obj.closest('table').find('.min-money-span'),
                            shopSumPrice = parseFloat(shopSumPriceControl.data('price'));
                    var ShopMinMoney = parseFloat(shopMinMoneySpan.data('money'));
                    if (discount) {
                        sumDiscount = sumDiscount.add(discount);
                        shopSumPriceControl.html((shopSumPrice.sub(discount)) + '(' + shopSumPrice + '-' + discount + ')');
                        if (shopSumPrice.sub(discount) > ShopMinMoney) {
                            shopMinMoneySpan.html('满足最低配送额¥' + ShopMinMoney);
                        } else {
                            shopMinMoneySpan.html('不满足最低配送额¥' + ShopMinMoney);
                        }
                    } else {
                        shopSumPriceControl.html(shopSumPrice.toFixed(2));
                        if (shopSumPrice.toFixed(2) > ShopMinMoney) {
                            shopMinMoneySpan.html('满足最低配送额¥' + ShopMinMoney);
                        } else {
                            shopMinMoneySpan.html('不满足最低配送额¥' + ShopMinMoney);
                        }

                    }

                });

                if (sumDiscount > 0) {
                    sumDiscountControl.html(sumDiscount.toFixed(2)).parents('.count').show();

                } else {
                    sumDiscountControl.html(sumDiscount).parents('.count').hide();
                }
                amountControl.html(parseFloat(sumPriceControl.data('sumPrice')).sub(sumDiscount));
            },
            shopMinMoney: function () {
                var shopItem = $('.shop-item'), shopIds = [], shippingAddressId = $('select[name="shipping_address_id"]').val();
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
                        $('.shop-min-money-' + shopMinMoney[i]['shop_id']).html(shopMinMoney[i]['min_money']);
                    }

                }).fail(function (jqXHR, textStatus, errorThrown) {


                }).always(function (data, textStatus, jqXHR) {

                });
            },
            deliveryMode: function (deliveryMode) {
                var goodsItem = $('.goods-item'), goodsIds = [], deliveryMode = deliveryMode || 1;  //deliveryMode: 1送货 2自提
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
                    confirmFunc.formatShopSumPrice();
                    confirmFunc.formatSumPrice();
                    confirmFunc.formatCouponControl();
                    confirmFunc.discountAmount();

                }).fail(function (jqXHR, textStatus, errorThrown) {


                }).always(function (data, textStatus, jqXHR) {

                });
            },
            formatShopSumPrice: function () {
                $('.shop-item').each(function () {
                    var obj = $(this), sumPrice = 0, sumPricePanel = obj.find('.shop-sum-price'), goodsItem = obj.find('.goods-item');
                    goodsItem.each(function () {
                        var self = $(this),
                                goodsPrice = parseFloat(self.find('.goods-price').data('price')),
                                goodsNum = self.find('.goods-num').data('num');
                        sumPrice = sumPrice.add(goodsPrice.mul(goodsNum));
                    });


                    sumPricePanel.data('price', sumPrice).html(sumPrice.toFixed(2));
                })
            },
            formatSumPrice: function () {
                var sumPricePanel = $('.sum-price'), sumPrice = 0;
                $('.shop-item').each(function () {
                    var shopSumPrice = $(this).find('.shop-sum-price').data('price');
                    sumPrice = sumPrice.add(parseFloat(shopSumPrice));
                });

                sumPricePanel.data('sum-price', sumPrice).html(sumPrice);
            },
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

        $(".default-checked").click(function (e) {
            $(this).siblings(".option-wrap").slideToggle();
        })

        $(".option-item").click(function () {
            var checkedHtml = $(this).html(), self = $(this);

            self.hasClass('address-select') && $('input[name="shipping_address_id"]').val(self.data('id'));
            self.hasClass('pay_way_select') &&  $('input[name="pay_way"]').val(self.data('value'));
            self.parents(".option-wrap").siblings(".default-checked").children(".content").html(checkedHtml);
            self.parents(".option-wrap").slideUp();
        })

        $('.option-panel').click(function (e) {
            e.stopPropagation();
        })

        $(document).click(function (e) {
            e = e || window.event;
            if (e.target != $('.option-panel')[0]) {
                $(".option-panel .option-wrap").slideUp();
            }
        });
    </script>
@stop

