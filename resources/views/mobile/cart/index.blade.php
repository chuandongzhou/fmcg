@extends('mobile.master')

@section('subtitle', '店铺')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item shopping-nav">
        <div class="row nav-top margin-clear">
            <div class="col-xs-10  pd-right-clear">
                购物车(<span>{{ $cartNum }}</span>)
            </div>
            <div class="col-xs-2 edit-btn pd-clear">
                <a class="edit">编辑</a>
                <input type="button" class="submit-btn hidden" value="完成">
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <form action="{{ url('api/v1/order/confirm-order') }}" method="post" class="mobile-ajax-form"
          data-delay="0">
        <div class="container-fluid  m60 p105">
            @foreach($carts as $shop)
                <div class="row cart-commodity">
                    <div class="col-xs-12 shop-name-panel row-panel">
                        <div class="checkbox-item item pull-left">
                            <input type="checkbox" id="shop{{ $shop->id }}" class="check shop-check" checked>
                            <label for="shop{{ $shop->id }}"></label>
                        </div>
                        <div class="item pull-left">
                            <i class="iconfont icon-shangpu"></i>{{ $shop->name }}<span
                                    class="small">({{ cons()->valueLang('user.type', $shop->user_type) }})</span>
                        </div>
                        <div class="item pull-right small">
                            最低配送额 ： ¥{{ $shop->min_money }}
                        </div>
                    </div>
                    @foreach($shop->cart_goods as $cart)
                        <div class="col-xs-12 row-panel commodity-wrap">
                            <div class="item pull-left middle-item check-item">
                                <input type="checkbox" name="ids[]" value="{{ $cart->goods_id }}"
                                       id="goods{{ $cart->goods_id }}" class="check goods-check"
                                       checked><label
                                        for="goods{{ $cart->goods_id }}"></label>
                            </div>
                            <a href="{{ url('goods/' . $cart->goods_id) }}">
                                <div class="item pull-left middle-item">
                                    <img class="commodity-img" src="{{ $cart->image }}"/>
                                </div>
                            </a>
                            <div class="item pull-right commodity-panel">
                                <div class="first">
                                    <div class="commodity-name">{{ $cart->goods->name }}</div>
                                    <div class="num-panel">
                                        <div class="price red pull-left">
                                            ¥{{ $cart->goods->price . '/' . $cart->goods->pieces }}</div>
                                        <div class="goods-num pull-right">x{{ $cart->num }}</div>
                                    </div>
                                </div>
                                <div class="new hidden">
                                    <div class="pull-left enter-panel">
                                        <i class="less iconfont icon-jian desc-num"
                                           data-group="group{{ $cart->goods_id }}"></i>
                                        <div class="enter-num">
                                            <input class="text-center num" type="text"
                                                   name="num[{{ $cart->goods_id }}]"
                                                   value="{{ $cart->goods->min_num }}"
                                                   data-min-num="{{ $cart->goods->min_num }}"
                                                   data-price="{{ $cart->goods->price }}"
                                                   data-group="group{{ $cart->goods_id }}"/>
                                            <span class="min-num">最低购买数{{ $cart->goods->min_num }}</span>
                                        </div>
                                        <i class="plus iconfont icon-jia inc-num"
                                           data-group="group{{ $cart->goods_id }}"></i>
                                    </div>
                                    <div class="pull-right remove">
                                        <a class="red mobile-ajax goods-delete"
                                           data-url="{{ url('api/v1/cart/delete/'.$cart->id ) }}"
                                           data-method="delete" data-danger="是否从购物车移除该商品？" data-no-prompt="true"
                                           data-done-then="none">删除</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="fixed-cart">
            <div class="item all-check pull-left">
                <label><input type="checkbox" id="check-all" class="check check-all" checked><label
                            for="check-all"></label>全选</label>
            </div>
            <div class="item pull-right">
                <div class="total">总额：<span class="red total-amount">¥{{ $carts->sum('sum_price') }}</span></div>
                <button class="submit confirm" type="submit" data-no-prompt="true"
                        data-done-url="{{ url('order/confirm-order') }}">
                    确认结算
                </button>
                <button class="batch-delete submit hidden mobile-ajax" data-data='{"type": "pc"}'
                        data-url="{{ url('api/v1/cart/batch-delete') }}"
                        data-method="delete" data-done-then="none">删除
                </button>
            </div>
        </div>
    </form>
@stop

@include('mobile.includes.footer')

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            /*onCheckChange('.parent-children', '.children');*/

            //编辑操作
            $(".edit-btn .edit").click(function () {
                $(this).addClass("hidden").siblings(".submit-btn").removeClass("hidden");
                $(".commodity-panel .first").addClass("hidden").siblings(".new").removeClass("hidden");
                $(".total").addClass("hidden").siblings(".batch-delete").removeClass('hidden').siblings('.confirm').addClass('hidden');
            })

            //编辑完成操作
            $(".edit-btn .submit-btn").click(function () {
                $(this).addClass("hidden").siblings(".edit").removeClass("hidden");
                $(".commodity-panel .new").addClass("hidden").siblings(".first").removeClass("hidden");
                $(".total").removeClass("hidden").siblings(".batch-delete").addClass('hidden').siblings('.confirm').removeClass('hidden');
                completeTrigger(true);
            })

            //商品删除
            $('.goods-delete').on('done.hct.ajax', function () {
                $(this).closest('.commodity-wrap').fadeOut(500, function () {
                    $(this).remove();
                });
            });
            //批量删除
            $('.batch-delete').on('done.hct.ajax', function () {
                $('.cart-commodity').find('.check:checked').each(function () {
                    var obj = $(this);
                    if (obj.hasClass('shop-check')) {
                        obj.closest('.cart-commodity').fadeOut(500, function () {
                            $(this).remove();
                        })
                    } else {
                        obj.closest('.commodity-wrap').fadeOut(500, function () {
                            $(this).remove();
                        });
                    }
                });

            });

            var checkAll = $('.check-all')
                , shopCheck = $('.shop-check')
                , goodsCheck = $('.goods-check')
                , shopAndGoodsCheck = $('.shop-check, .goods-check');

            checkAll.change(function () {
                shopAndGoodsCheck.prop('checked', $(this).prop('checked'));
                goodsCheck.each(function () {
                    $(this).triggerHandler('change')
                });
            });

            shopCheck.change(function () {
                var shopGoodsCheck = $(this).closest('.cart-commodity').find('.goods-check');
                shopGoodsCheck.prop('checked', $(this).prop('checked'))
                shopGoodsCheck.each(function () {
                    $(this).triggerHandler('change')
                });
                allCheck(checkAll, shopAndGoodsCheck);
            });

            goodsCheck.on('change', function () {
                var obj = $(this)
                    , cartCommodity = obj.closest('.cart-commodity')
                    , shopCheck = cartCommodity.find('.shop-check')
                    , goodsCheck = cartCommodity.find('.goods-check');
                obj.closest('.commodity-wrap').find('input.num').prop('disabled', !obj.prop('checked'));
                completeTrigger();

                allCheck(shopCheck, goodsCheck);
                allCheck(checkAll, shopAndGoodsCheck);
            });

            /**
             * 全选按钮
             */
            var allCheck = function (parent, child) {
                parent.prop('checked', child.length === child.filter(':checked').length);
            };

            //页面加载完成成调用
            var completeTrigger = function (updateNum) {
                var amount = 0;
                //计算总价格
                $('.commodity-wrap').each(function () {
                    var obj = $(this)
                        , goodsNum = obj.find('.goods-num')
                        , numInput = obj.find('.num')
                        , numInputValue = parseInt(numInput.val());
                    updateNum && goodsNum.html('x' + numInputValue);

                    if (!numInput.prop('disabled')) {
                        amount = amount.add(numInputValue.mul(numInput.data('price')));
                    }
                });
                $('.total-amount').html('¥' + amount);
            };
            completeTrigger();

            numChange();
        })
    </script>
@stop