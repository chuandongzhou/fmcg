@section('js')
    @parent
    <script type="text/javascript">
        jQuery(function ($) {
            //创建DOM
            var
                    quickHTML = document.querySelector("div.quick_link_mian"),
                    quickShell = $(document.createElement('div')).html(quickHTML).addClass('quick_links_wrap'),
                    quickLinks = quickShell.find('.quick_links'),
                    quickPanel = quickLinks.next();
            quickShell.appendTo('.mui-mbar-tabs');

            //具体数据操作
            var
                    quickPopXHR,
                    popTmpl = '<a href="javascript:;" class="ibar_closebtn" title="关闭"></a><div class="ibar_plugin_title"><h3><\%=title\%></h3></div><div class="pop_panel"><\%=content\%></div><div class="arrow"><i></i></div><div class="fix_bg"></div>',
                    quickPop = quickShell.find('#quick_links_pop'),
                    quickDataFns = {
                        //购物信息
                        cart: {
                            title: '购物车',
                            content: '',
                            init: $.noop,
                            afterShow: getCartGoods
                        },

                        //我的资产
                        my_assets: {
                            title: '我的资产',
                            content: '',
                            init: $.noop,
                            afterShow: getCoupon
                        },
                        //给客服留言
                        leave_message: {
                            title: '我关注的产品',
                            content: $("#ibar_gzcp").html(),
                            init: $.noop,

                        },
                        mpbtn_histroy: {
                            title: '我的足迹',
                            content: '<div class="ibar_plugin_content"><div class="ibar-history-head">共3件产品<a href="#">清空</a></div><div class="ibar-moudle-product"><div class="imp_item"><a href="#" class="pic"><img src="http://placehold.it/85" width="100" height="100" /></a><p class="tit"><a href="#">夏季透气真皮豆豆鞋反绒男士休闲鞋韩</a></p><p class="price"><em>¥</em>649.00</p><a href="#" class="imp-addCart">加入购物车</a></div><div class="imp_item"><a href="#" class="pic"><img src="http://placehold.it/85" width="100" height="100" /></a><p class="tit"><a href="#">夏季透气真皮豆豆鞋反绒男士休闲鞋韩</a></p><p class="price"><em>¥</em>649.00</p><a href="#" class="imp-addCart">加入购物车</a></div><div class="imp_item"><a href="#" class="pic"><img src="http://placehold.it/85" width="100" height="100" /></a><p class="tit"><a href="#">夏季透气真皮豆豆鞋反绒男士休闲鞋韩</a></p><p class="price"><em>¥</em>649.00</p><a href="#" class="imp-addCart">加入购物车</a></div></div></div>',
                            init: $.noop
                        },
                        mpbtn_wdsc: {
                            title: '收藏的产品',
                            content: '<div class="ibar_plugin_content"><div class="ibar_cart_group ibar_cart_product"><ul><li class="cart_item"><div class="cart_item_pic"><a href="#"><img src="http://placehold.it/85" /></a></div><div class="cart_item_desc"><a href="#" class="cart_item_name">夏季透气真皮豆豆鞋反绒男士休闲鞋韩版磨砂驾车鞋英伦船鞋男鞋子</a><div class="cart_item_sku"><span>尺码：38码（精工限量版）</span></div><div class="cart_item_price"><span class="cart_price">¥700.00</span><a href="#" class="sc" title="删除"><span class="fa fa-trash"></span></a></div></div>	</li></ul></div><div class="cart_handler"><a href="#" class="cart_go_btn jiaru" target="_blank">加入购物车</a></div></div>',
                            init: $.noop
                        },
                        mpbtn_recharge: {
                            title: '手机充值',
                            content: '<div class="ibar_plugin_content"><form target="_blank" class="ibar_recharge_form"><div class="ibar_recharge-field"><label>号码</label><div class="ibar_recharge-fl"><div class="ibar_recharge-iwrapper"><input type="text" name="19" placeholder="手机号码" /></div><i class="ibar_recharge-contact"></i></div></div><div class="ibar_recharge-field"><label>面值</label><div class="ibar_recharge-fl"><p class="ibar_recharge-mod"><span class="ibar_recharge-val">100</span>元</p><i class="ibar_recharge-arrow"></i><div class="ibar_recharge-vbox"><ul style="display:none;"><li><span>10</span>元</li><li class="sanwe selected"><span>100</span>元</li><li><span>20</span>元</li><li class="sanwe"><span>200</span>元</li><li><span>30</span>元</li><li class="sanwe"><span>300</span>元</li><li><span>50</span>元</li><li class="sanwe"><span>500</span>元</li></ul></div></div></div><div class="ibar_recharge-btn"><input type="submit" value="立即充值" /></div></form></div>',
                            init: $.noop
                        }
                    };

            //showQuickPop
            var
                    prevPopType,
                    prevTrigger,
                    doc = $(document),
                    popDisplayed = false,
                    hideQuickPop = function () {
                        if (prevTrigger) {
                            prevTrigger.removeClass('current');
                        }
                        popDisplayed = false;
                        prevPopType = '';
                        quickPop.hide();
                        $(".mui-mbar-tabs,.quick_links_wrap").css("width", "0");
                        quickPop.animate({left: 280, queue: true});
                    },
                    showQuickPop = function (type) {
                        if (quickPopXHR && quickPopXHR.abort) {
                            quickPopXHR.abort();
                        }
                        var fn = quickDataFns[type];
                        if (type !== prevPopType) {
                            fn['content'] = fn['content'] ? fn['content'] : getContent(type);
                            quickPop.html(ds.tmpl(popTmpl, fn));
                            fn.init.call(this, fn);
                        }
                        doc.unbind('click.quick_links').one('click.quick_links', hideQuickPop);

                        quickPop[0].className = 'quick_links_pop quick_' + type;
                        popDisplayed = true;
                        prevPopType = type;
                        quickPop.show();
                        quickPop.animate({left: 0, queue: true});
                        if (fn['afterShow']) {
                            fn['afterShow'].call(this);
                        }
                    };
            quickShell.bind('click.quick_links', function (e) {
                $(".mui-mbar-tabs,.quick_links_wrap").css("width", "320px");
                e.stopPropagation();
            });
            quickPop.delegate('a.ibar_closebtn', 'click', function () {
                quickPop.hide();
                quickPop.animate({left: 280, queue: true});
                if (prevTrigger) {
                    prevTrigger.removeClass('current');
                }
            });

            //通用事件处理
            var
                    view = $(window),
                    quickLinkCollapsed = !!ds.getCookie('ql_collapse'),
                    getHandlerType = function (className) {
                        return className.replace(/current/g, '').replace(/\s+/, '');
                    },
                    showPopFn = function () {
                        var type = getHandlerType(this.className);
                        if (popDisplayed && type === prevPopType) {
                            return hideQuickPop();
                        }
                        showQuickPop(this.className);
                        if (prevTrigger) {
                            prevTrigger.removeClass('current');
                        }
                        prevTrigger = $(this).addClass('current');
                    },

                    quickHandlers = {
                        //购物车，最近浏览，商品咨询
//                        my_qlinks: showPopFn,
                        cart: showPopFn,
                        my_assets: showPopFn,
//                        leave_message: showPopFn,
//                        mpbtn_histroy: showPopFn,
//                        mpbtn_recharge: showPopFn,
//                        mpbtn_wdsc: showPopFn,
                        //返回顶部
                        return_top: function () {
                            ds.scrollTo(0, 0);
                            hideReturnTop();
                        }
                    };
            quickShell.delegate('a', 'click', function (e) {
                var type = getHandlerType(this.className);

                if (type && quickHandlers[type]) {
                    quickHandlers[type].call(this);
                    e.preventDefault();
                }
            });

            //Return top
            var scrollTimer, resizeTimer, minWidth = 1350;

            function resizeHandler() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(checkScroll, 160);
            }

            function checkResize() {
                quickShell[view.width() > 1340 ? 'removeClass' : 'addClass']('quick_links_dockright');
            }

            function scrollHandler() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(checkResize, 160);
            }

            function checkScroll() {
                view.scrollTop() > 100 ? showReturnTop() : hideReturnTop();
            }

            function showReturnTop() {
                quickPanel.addClass('quick_links_allow_gotop');
            }

            function hideReturnTop() {
                quickPanel.removeClass('quick_links_allow_gotop');
            }

            view.bind('scroll.go_top', resizeHandler).bind('resize.quick_links', scrollHandler);
            quickLinkCollapsed && quickShell.addClass('quick_links_min');
            resizeHandler();
            scrollHandler();


            //新加的js

            $(".quick_links_panel li").mouseenter(function () {
                var self = $(this);
                self.children(".mp_tooltip").animate({left: -92, queue: true});
                self.children(".mp_tooltip").css("visibility", "visible");
                self.children(".ibar_login_box").css("display", "block");
            });
            $(".quick_links_panel li").mouseleave(function () {
                var self = $(this);
                self.children(".mp_tooltip").css("visibility", "hidden");
                self.children(".mp_tooltip").animate({left: -121, queue: true});
                self.children(".ibar_login_box").css("display", "none");
            });
            $(".quick_toggle li").mouseover(function () {
                $(this).children(".mp_qrcode").show();
            });
            $(".quick_toggle li").mouseleave(function () {
                $(this).children(".mp_qrcode").hide();
            });

            $("#quick_links_pop").on("click", ".receive-wrap", function () {
                var obj = $(this),
                        couponId = obj.data('id'),
                        couponWrap = obj.closest('.coupon'),
                        notReceive = couponWrap.find('.not-receive'),
                        receiveWrap = couponWrap.find('.receive-wrap');

                $.ajax({
                    url: site.api('coupon/receive/' + couponId),
                    method: 'post',
                    data: {},
                    dataType: 'json',
                }).done(function () {
                    notReceive.css("display", "none").siblings().css("display", "inline-block");
                    setTimeout(function () {
                        receiveWrap.css("display", "none");
                    }, 500)
                }).fail(function (jqXHR) {
                    var json = jqXHR['responseJSON'];
                    notReceive.css("display", "none").siblings().css("display", "inline-block").html('<span class="fa fa-remove"></span>' + json['message']);
                    setTimeout(function () {
                        notReceive.css("display", "inline-block").siblings().css("display", "none").html('<span class="fa fa-check"></span>已领');
                    }, 500)

                }).always(function () {

                });


            });

            function getContent(type) {
                if (type == 'my_assets') {
                    return '<div class="ibar_plugin_content">' +
                            '    <div class="ia-head-list"><a href="{{ url('coupons') }}" target="_blank" class="pl">' +
                            '            <div class="my-coupon-num">0</div>' +
                            '            <div class="text">优惠券</div>' +
                            '        </a></div>' +
                            '    <div class="ga-expiredsoon">' +
                            '        <div class="es-head">即将过期优惠券</div>' +
                            '        <div class="coupon-wrap my-coupon-wrap">' +
                            '              <div class="loading-img"><img src="../images/loading.gif" /> </div>' +
                            '        </div>' +
                            '    </div>' +
                            '    <div class="ga-expiredsoon">' +
                            '        <div class="es-head">当前店铺有可领优惠券哦</div>' +
                            '        <div class="coupon-wrap shop-coupon-wrap">' +
                            '              <div class="loading-img"><img src="../images/loading.gif" /> </div>' +
                            '        </div>' +
                            '    </div>' +
                            '</div>';
                } else if (type == 'cart') {
                    return '<div class="ibar_plugin_content">' +
                            '    <div class="ibar_cart_group ibar_cart_product">' +
                            '        <div class="ibar_cart_group_header"><span class="ibar_cart_group_title"></span><a href="{{ url('cart') }}">我的购物车</a></div>' +
                            '        <ul class="cart_wrap">' +
                            '              <div class="loading-img"><img src="../images/loading.gif" /> </div>' +
                            '        </ul>' +
                            '    </div>' +
                            '    <div class="cart_handler">' +
                            '        <div class="cart_handler_header"><span class="cart_handler_left">共<span class="goods_num">0</span>件商品</span>' +
                            '            <span class="cart_handler_right">¥<span class="total_price">0</span></span></div>' +
                            '        <a href="{{ url('cart') }}" class="cart_go_btn" target="_blank">去购物车结算</a></div>' +
                            '</div>';
                }
            }

            function getCoupon() {
                var shopCouponWrap = $('.shop-coupon-wrap'),
                        myCouponWrap = $('.my-coupon-wrap');

                getShopCoupon(shopCouponWrap);
                getUserCoupon(myCouponWrap);
            }

            /**
             * 获取店铺优惠券
             * @param shopCouponWrap
             */
            function getShopCoupon(shopCouponWrap) {
                var shopId = '{{ request()->is('shop/*') ? $shop->id : 0 }}'
                if (shopId === '0') {
                    shopCouponWrap.closest('.ga-expiredsoon').remove();
                    return;
                }
                $.ajax({
                    url: site.api('coupon/' + shopId),
                    method: 'get',
                    data: {},
                    dataType: 'json',
                }).done(function (data) {
                    var coupons = data['coupons'], shopCouponHtml = '';
                    if (coupons.length) {
                        for (var i in coupons) {
                            var coupon = coupons[i];
                            shopCouponHtml += '            <div class="coupon bgc-orange">'
                            shopCouponHtml += '                <div class="receive-wrap"  data-id="' + coupon['id'] + '">'
                            shopCouponHtml += '                   <a class="not-receive">立即领取</a>'
                            shopCouponHtml += '                   <a class="already-receive">'
                            shopCouponHtml += '                       <span class="fa fa-check"></span>已领'
                            shopCouponHtml += '                   </a>'
                            shopCouponHtml += '               </div>'
                            shopCouponHtml += '                <div class="validity"><p>有效时间</p>'
                            shopCouponHtml += '                    <p>' + coupon['start_at'] + '</p>'
                            shopCouponHtml += '                    <p>' + coupon['end_at'] + '</p></div>'
                            shopCouponHtml += '                <ul>'
                            shopCouponHtml += '                    <li>' + coupon['shop']['name'] + '</li>'
                            shopCouponHtml += '                    <li>¥' + coupon['discount'] + '</li>'
                            shopCouponHtml += '                    <li>满' + coupon['full'] + '使用</li>'
                            shopCouponHtml += '                </ul>'
                            shopCouponHtml += '            </div>';
                        }
                        shopCouponWrap.append(shopCouponHtml);
                    } else {
                        shopCouponWrap.closest('.ga-expiredsoon').remove();
                    }
                }).fail(function (jqXHR) {
                    shopCouponWrap.closest('.ga-expiredsoon').remove();
                }).always(function () {
                    shopCouponWrap.find('.loading-img').remove();
                });
            }

            /**
             * 获取我的优惠券
             * @param myCouponWrap
             */
            function getUserCoupon(myCouponWrap) {
                $.ajax({
                    url: site.api('coupon/user-coupon/expire'),
                    method: 'get',
                    data: {},
                    dataType: 'json',
                }).done(function (data) {
                    var coupons = data['coupons'], UserCouponHtml = '', myCouponNum = $('.my-coupon-num');
                    if (coupons.length) {
                        myCouponNum.html(coupons.length);
                        for (var i in coupons) {
                            var coupon = coupons[i];

                            if (coupon['diff_time']) {
                                UserCouponHtml += '            <div class="coupon bgc-red">';
                                UserCouponHtml += '                <div class="expiration"><span>' + coupon['diff_time'] + '后过期</span></div>';
                                UserCouponHtml += '                <ul>';
                                UserCouponHtml += '                    <li><a href=" ' + site.url('shop/' + coupon['shop']['id']) + '" target="_blank">' + coupon['shop']['name'] + '</a></li>';
                                UserCouponHtml += '                    <li>¥' + coupon['discount'] + '</li>';
                                UserCouponHtml += '                    <li>满' + coupon['full'] + '使用</li>';
                                UserCouponHtml += '                </ul>';
                                UserCouponHtml += '            </div>';
                            } else {
                                UserCouponHtml += '            <div class="coupon bgc-blue">';
                                UserCouponHtml += '                <div class="validity"><p>有效期</p>';
                                UserCouponHtml += '                    <p>' + coupon['start_at'] + '</p>';
                                UserCouponHtml += '                    <p>' + coupon['end_at'] + '</p></div>';
                                UserCouponHtml += '               <ul>';
                                UserCouponHtml += '                    <li><a href=" ' + site.url('shop/' + coupon['shop']['id']) + '"  target="_blank">' + coupon['shop']['name'] + '</a></li>';
                                UserCouponHtml += '                    <li>¥' + coupon['discount'] + '</li>';
                                UserCouponHtml += '                    <li>满' + coupon['full'] + '使用</li>';
                                UserCouponHtml += '                </ul>';
                                UserCouponHtml += '            </div>';
                            }

                        }
                        myCouponWrap.append(UserCouponHtml);
                    } else {
                        myCouponWrap.closest('.ga-expiredsoon').remove();
                    }
                }).fail(function (jqXHR) {
                    myCouponWrap.closest('.ga-expiredsoon').remove();
                }).always(function () {
                    myCouponWrap.find('.loading-img').remove();
                });
            }

            function getCartGoods() {
                var cartWrap = $('.cart_wrap');
                $.ajax({
                    url: site.api('cart/index'),
                    method: 'get',
                    data: {},
                    dataType: 'json',
                }).done(function (data) {
                    var shops = data['shops'],
                            shopHtml = '',
                            goodsNumControl = $('.cart_handler_left .goods_num'),
                            totalPriceControl = $('.cart_handler_right .total_price');
                    if (shops.length) {
                        var type = '{{ auth()->user() ? auth()->user()->type_name : 'retailer' }}',
                                goodsNum = 0,
                                totalPrice = 0;

                        for (var i in shops) {
                            var shop = shops[i];
                            shopHtml += '            <li class="cart_item">'
                            shopHtml += '               <div class="store-name"> <a href="' + site.url('shop/' + shop['id']) + '"  target="_blank">' + shop['name'] + '</a></div>'
                            for (var j in shop['cart_goods']) {
                                var goods = shop['cart_goods'][j],
                                        goodsPrice = goods['goods']['price_' + type];
                                goodsNum = goodsNum + 1;
                                totalPrice = totalPrice.add(parseFloat(goodsPrice));
                                shopHtml += '                   <div class="store-panel">'
                                shopHtml += '                       <div class="cart_item_pic"><a href="' + site.url('goods/' + goods['goods_id']) + '" target="_blank"><img src="' + goods['image'] + '"/></a></div>'
                                shopHtml += '                       <div class="cart_item_desc"><a href="' + site.url('goods/' + goods['goods_id']) + '" class="cart_item_name">' + goods['goods']['name'] + '</a>'
                                shopHtml += '                           <div class="cart_item_price"><span class="cart_price">¥' + goodsPrice + '</span></div>'
                                shopHtml += '                       </div>'
                                shopHtml += '                   </div>'
                            }

                            shopHtml += '            </li>';

                        }
                        cartWrap.append(shopHtml);
                        goodsNumControl.html(goodsNum);
                        totalPriceControl.html(totalPrice);
                    }
                }).fail(function (jqXHR) {

                }).always(function () {
                    cartWrap.find('.loading-img').remove();
                });


            }
        });

    </script>
@stop