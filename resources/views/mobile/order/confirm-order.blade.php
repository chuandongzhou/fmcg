@extends('mobile.master')

@section('subtitle', '确认订单')

@include('includes.order-refund')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item shopping-nav">
        <div class="row nav-top margin-clear">
            <div class="col-xs-12  pd-right-clear">
                确认订单
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <form class="mobile-ajax-form" action="{{ url('api/v1/order/submit-order') }}" method="post"
          data-done-url="{{ url('order/success-order') }}">
        <div class="container-fluid  m60 p65">
            <div class="row">
                <div class="col-xs-12 shopping-ideas">
                    <div class="row ">
                        <div class="col-xs-3 prompt pd-right-clear text-left">提货方式</div>
                        <div class="col-xs-4 item-btn">
                            <label class="btn  self-take">自提
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </label>
                        </div>
                        <div class="col-xs-4 item-btn">
                            <label class="btn active btn-delivery">送货
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 delivery-wrap">
                    <div class="row item">
                        <div class="col-xs-11 pd-right-clear left-panel address-panel">
                            <div class="delivery-info"><span class="prompt">收货信息</span>
                                <span class="name-info">{{ $defaultAddress->consigner . ' -- ' . $defaultAddress->phone }}</span>
                            </div>
                            <div class="address">{{ $defaultAddress->address_name }}</div>
                        </div>
                        <div class="col-xs-1 right-icon">
                            <i class="iconfont icon-jiantouyoujiantou"></i>
                        </div>
                        <input type="hidden" name="shipping_address_id" value="{{ $defaultAddress->id }}">
                    </div>
                    <div class="row item shopping-ideas pd-clear">
                        <div class="col-xs-3 pd-right-clear prompt text-left">支付方式</div>
                        <div class="col-xs-4 item-btn">
                            <label class="btn active" for="pay-type-online">在线支付
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </label>
                            <input class="hidden" checked type="radio" id="pay-type-online" name="pay_type"
                                   value="online">
                        </div>
                        <div class="col-xs-5 item-btn ">
                            <label class="btn cash-on-delivery" for="pay-type-cod">货到付款:<span
                                        class="pay-way-name">现金</span>
                                <span class="triangle"></span>
                                <span class="fa fa-check"></span>
                            </label>
                            <input class="hidden" type="radio" id="pay-type-cod" name="pay_type"
                                   value="cod">
                            <input type="hidden" name="pay_way" value="cash">
                        </div>

                    </div>
                </div>
            </div>
            @foreach($shops as $shop)
                <div class="row cart-commodity confirm-order shop-{{ $shop->id }}" data-id="{{ $shop->id }}">
                    <div class="col-xs-12 shop-name-panel row-panel">
                        <div class="item pull-left shop-name">
                            <div>
                                <i class="iconfont icon-shangpu"></i>{{ $shop->name }}
                            </div>
                            <span class="small">({{ cons()->valueLang('user.type', $shop->user_type) }})</span>
                        </div>
                        <div class="item pull-right small min-money-panel">
                            最低配送额 ¥<span class="shop-min-money"
                                         data-min-money="{{ $shop->min_money }}">{{ $shop->min_money }}</span>
                        </div>
                    </div>
                    @foreach($shop->cart_goods as $goods)
                        <div class="col-xs-12 row-panel commodity-wrap goods-item goods-{{ $goods->goods_id }}"
                             data-id="{{ $goods->goods_id }}">
                            <a href="{{ url('goods/' . $goods->goods_id) }}">
                                <div class="item pull-left middle-item">
                                    <img class="commodity-img" src="{{ $goods->image }}"/>
                                </div>
                            </a>
                            <div class="item pull-right commodity-panel">
                                <div class="commodity-name">{{ $goods->goods->name }}</div>
                                <div class="num-panel">
                                    <div class="price red pull-left">
                                    <span class="goods-price" data-price="{{ $goods->goods->price }}">
                                        {{ $goods->goods->price }}</span>{{ '/' . $goods->goods->pieces }}
                                    </div>
                                    <div class="num pull-right" data-num="{{ $goods->num }}">x{{ $goods->num }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="col-xs-12 other-opera">
                        <div class="bordered">
                            <div class="coupon clearfix">
                                <span class="pull-left prompt">优惠券 :</span>
                                <a class="pull-right coupon-option" data-shop-id="{{ $shop->id }}">
                                    <span class="red coupon-content">不使用优惠</span> <i class="iconfont icon-xiala"></i>
                                </a>
                                <input type="hidden" name="shop[{{ $shop->id }}][coupon_id]" data-discount="0"
                                       class="coupon-id" value="0">
                            </div>
                            <div class="remark clearfix">
                                <span class="pull-left prompt">备注 :</span>
                                <input name="shop[{{ $shop->id }}][remark]" class="control" type="text"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!--优惠券 弹出层-->
                <div class="popover-wrap popover-coupon-{{ $shop->id }}">
                    <div class="popover-panel">
                        <div class="title text-center coupon-title">店铺优惠</div>
                        <ul class="coupon-panel" data-shop-id="{{ $shop->id }}">
                            @foreach($shop->coupons as $coupon)
                                <li data-id="{{ $coupon->id }}" data-full="{{ $coupon->full }}"
                                    data-discount="{{ $coupon->discount }}">
                                    <span class="pull-left">满{{ $coupon->full }}减{{ $coupon->discount }}</span>
                                </li>
                            @endforeach
                            <li data-id="0" data-full="0" data-discount="0">
                                <span class="pull-left">不使用优惠</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="fixed-cart confirm-order-fixed">
            <div class="item pull-right">
                <div class="total">应付总额：<span class="red"></span></div>
                <button type="submit" class="submit" value="提交订单">提交订单</button>
            </div>
        </div>
    </form>


    <!--支付方式 弹出层-->
    <div class="popover-wrap popover-pay">
        <div class="popover-panel">
            <div class="title text-center coupon-title">请选择货到支付方式</div>
            <ul class="coupon-panel pay-panel">
                <li data-way="cash">
                    <button class="pay-btn">现金</button>
                </li>
                <li data-way="card">
                    <button class="pay-btn">刷卡</button>
                </li>
            </ul>
        </div>
    </div>

    <!--地址弹出层-->
    <div class="popover-wrap popover-address">
        <div class="popover-panel">
            <div class="title text-center address-title">收货信息</div>
            <div class="address-wrap">
                @foreach($shippingAddress as $item)
                    <div class="address-list-item {{ $item->is_default ? 'active' : '' }}" data-id="{{ $item->id }}">
                        <div class="contact-person clearfix">
                            <div class="pull-left">{{ $item->consigner }} -- {{ $item->phone }}</div>
                            <div class="pull-right set-item">
                                <button class="btn btn-primary check">默认</button>
                            </div>
                        </div>
                        <div class="contact-address">{{ $item->address_name }}</div>
                    </div>
                @endforeach
            </div>
            <div class="footer text-center address-footer">
                <a href="{{ url('shipping-address/create') }}" class="btn btn-primary">新增收货地址</a>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {

            var Confirm = function () {
                this.isDelivery = true;
            };
            Confirm.prototype = {
                //店铺最低配送额
                shopMinMoney: function () {
                    var shopItem = $('.cart-commodity'), shopIds = [],
                        shippingAddressId = $('input[name="shipping_address_id"]').val();
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
                            var minMoney = shopMinMoney[i]['min_money']
                                , shopId = shopMinMoney[i]['shop_id']
                                , minMoneyPanel = $('.shop-' + shopId).find('.shop-min-money');
                            minMoneyPanel.data('minMoney', minMoney).html(minMoney);
                        }

                    }).fail(function (jqXHR, textStatus, errorThrown) {


                    }).always(function (data, textStatus, jqXHR) {
                        //that.overThanShopMinMoney();
                    });
                },
                //提货方式
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
                            var goodsId = goodsPrice[i]['goods_id'], goodsPanel = $('.goods-' + goodsId),
                                price = goodsPrice[i]['price'];

                            goodsPanel.find('.goods-price').data('price', price).html(price);
                        }
                        confirm.isDelivery = (deliveryMode === 1);

                    }).fail(function (jqXHR, textStatus, errorThrown) {


                    }).always(function (data, textStatus, jqXHR) {
                        $('.coupon-content').html('不使用优惠');
                        $('.coupon-id').val(0).data('discount', 0);
                        confirm.formatAmount();
                    });
                },
                //店铺总价格
                shopSumPrice: function (shopId) {
                    var goodsItem = $('.shop-' + shopId).find('.goods-item'), sumPrice = 0;
                    goodsItem.each(function () {
                        var self = $(this),
                            goodsPrice = parseFloat(self.find('.goods-price').data('price')),
                            goodsNum = self.find('.num').data('num');
                        sumPrice = sumPrice.add(goodsPrice.mul(goodsNum));
                    });
                    return sumPrice;
                },

                //格式化总价格
                formatAmount: function () {
                    var that = this, shopItem = $('.cart-commodity'), amount = 0, discount = 0;
                    shopItem.each(function () {
                        var obj = $(this), shopId = obj.data('id');
                        amount = amount.add(that.shopSumPrice(shopId));
                        discount = discount.add(parseFloat(obj.find('.coupon-id').data('discount')));
                    });
                    var amountHtml = discount ? amount.sub(discount) + '(' + amount + '-' + discount + ')' : amount;
                    $('.total').find('.red').html('¥' + amountHtml);
                }
            };

            var confirm = new Confirm();
            confirm.shopMinMoney();
            confirm.formatAmount();

            $(".shopping-ideas .item-btn .btn").click(function () {
                var self = $(this);
                self.addClass("active").parents(".item-btn").siblings().children(".btn").removeClass("active");
                if (self.hasClass("btn-delivery")) {
                    confirm.deliveryMode({{ cons('order.delivery_mode.delivery') }});
                    $(".delivery-wrap").removeClass("hidden").find('input').each(function () {
                        $(this).prop('disabled', false);
                    });
                    $('.min-money-panel').removeClass('hide');
                } else if (self.hasClass("self-take")) {
                    confirm.deliveryMode({{ cons('order.delivery_mode.pick_up') }});
                    $(".delivery-wrap").addClass("hidden").find('input').each(function () {
                        $(this).prop('disabled', true);
                    });
                    $('.min-money-panel').addClass('hide');
                }
            });

            //点击出弹出层
            $(".coupon-option").click(function () {
                var shopId = $(this).data('shopId')
                    , sumPrice = confirm.shopSumPrice(shopId)
                    , popoverCoupon = $(".popover-coupon-" + shopId);
                popoverCoupon.find('li').each(function () {
                    var obj = $(this);
                    if (parseFloat(obj.data('full')) > sumPrice) {
                        obj.addClass('hide');
                    } else {
                        obj.removeClass('hide');
                    }
                });

                layer.open({
                    title: false,
                    content: popoverCoupon.html(),
                    style: ' width:95%; height: auto;  padding:0;',
                    shade: 'background-color: rgba(0,0,0,.3)'
                });
                $(".popover-panel").parent().addClass("pd-clear");
            });

            //选择优惠券
            $("body").on('click', '.coupon-panel li', function () {
                var obj = $(this)
                    , shopId = obj.parent().data('shopId')
                    , couponId = obj.data('id')
                    , couponContent = obj.children('.pull-left').html()
                    , shopPanel = $('.shop-' + shopId);
                shopPanel.find('.coupon-content').html(couponContent);
                shopPanel.find('.coupon-id').val(couponId).data('discount', obj.data('discount'));
                confirm.formatAmount();
                layer.closeAll();
            });

            //选择收货地址
            $('body').on('click', '.address-wrap .address-list-item', function () {
                var obj = $(this)
                    , deliveryWrap = $('.delivery-wrap')
                    , addressName = obj.find('.contact-address').html()
                    , contactInfo = obj.find('.contact-person').children('.pull-left').html()
                    , shippingAddressId = obj.data('id');
                $('input[name="shipping_address_id"]').val(shippingAddressId);
                deliveryWrap.find('.address').html(addressName);
                deliveryWrap.find('.name-info').html(contactInfo);
                confirm.shopMinMoney();
                layer.closeAll();
            });

            //选择货到付款支付方式
            $('body').on('click', '.pay-panel li', function () {
                var obj = $(this), payWayName = obj.children('.pay-btn').html();
                $('input[name="pay_way"]').val(obj.data('way'));
                $('.cash-on-delivery').find('.pay-way-name').html(payWayName);
            })

            //点击出弹出层
            $(".cash-on-delivery").click(function () {
                layer.open({
                    title: false,
                    content: $(".popover-pay").html(),
                    style: ' width:95%; height: auto;  padding:0;',
                    shade: 'background-color: rgba(0,0,0,.3)'
                });
                $(".popover-panel").parent().addClass("pd-clear");
            })

            //点击修改收货地址
            $(".address-panel").click(function () {
                layer.open({
                    type: 1,
                    content: $(".popover-address").html(),
                    anim: false,
                    style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
                });
            })
            $('.submit').on('click', function () {
                if (confirm.isDelivery) {
                    //验证店铺金额是否大于店铺最低配送额;
                    var allow = true;
                    $('.cart-commodity').each(function () {
                        var obj = $(this)
                            , shopId = obj.data('id')
                            , shopMoney = confirm.shopSumPrice(shopId)
                            , shopMinMoney = obj.find('.shop-min-money').data('minMoney');
                        if (shopMoney < shopMinMoney) {
                            showMassage('店铺不满足最低配送额');
                            allow = false;
                        }
                    });
                    return allow
                }

            })

        })
    </script>
@stop