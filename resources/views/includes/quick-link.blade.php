@section('js')
    @parent
    <script type="text/javascript">
        jQuery(function ($) {
            var flip = 0;
            var couponFlip = 0;
            //查询店铺是否有可领取优惠券
             @if(request()->is('shop/*')&& $shop->id!=$user->shop->id )
                var shop = '{{ request()->is('shop/*')? $shop->id : 0 }}'
                var url = site.api('coupon/coupon-num/' + shop);
                $.ajax({
                    url: url,
                    method: 'get'
                }).done(function (data) {
                    if (data.couponNum > 0) {
                        showOtherTooltip();
                    }
                })
            @endif
            //点击购物车图标显示弹框
            $("#shopCart").click(function (e) {
                hideOtherTooltip();

                e.stopPropagation();
                couponFlip = 0;
                if (flip++ % 2 === 0) {
                    var cart_head_html = '<a href="javascript:;" class="ibar_closebtn" title="关闭"><i class="fa fa-remove"></i></a>' +
                            '<div class="ibar_plugin_title">' +
                            '<h3>' +
                            '购物车' +
                            '</h3>' +
                            '</div>' +
                            '<div class="loading-img"><img src="../images/loading.gif"/></div>' +
                            '<div class="pop_panel cart-panel" >' +
                            '<div class="ibar_plugin_content"></div>' +
                            '</div>' +
                            '<div class="arrow"><i></i></div>' +
                            '<div class="fix_bg"></div>';
                    $('.quick_links_pop').html(cart_head_html);
                    $(".quick_links_pop").animate({left: -320, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1");

                    $.ajax({
                        url: site.api('cart'),
                        method: 'get'
                    }).done(function (data) {
                        var html = '', cartNum = 0, cartPrices = 0;
                        html += '<div class="ibar_cart_group ibar_cart_product" >' +
                                '<ul>';
                        var detailShops = data.shops;
                        for (var shop in detailShops) {
                            html += '<li class="cart_item">' +
                                    '<a href="/shop/' + detailShops[shop].id + '"><div class="store-name">' + detailShops[shop].name + '</div></a>';
                            cartPrices = cartPrices.add(detailShops[shop].sum_price);
                            for (var goods in detailShops[shop].cart_goods) {
                                cartNum++;
                                var detailGoods = detailShops[shop].cart_goods[goods];

                                html += '<div class="store-panel">' +
                                        '<div class="cart_item_pic"><a href="/goods/' + detailGoods.goods_id + '"><img src="' + detailGoods.image + '"></a></div>' +
                                        '<div class="cart_item_desc"><a href="/goods/' + detailGoods.goods_id + '" class="cart_item_name">' + detailGoods.goods.name + '</a>' +
                                        '<div class="cart_item_price"><span class="cart_price">¥' + detailGoods.goods.price + '</span></div>' +
                                        '</div>' +
                                        '</div>';
                            }
                            html += '</li>';
                        }

                        html += '</ul>' +
                                '</div>' +
                                '<div class="cart_handler">' +
                                '<div class="cart_handler_header"><span class="cart_handler_left">共<span' +
                                'class="cart_price cart_num">' + cartNum + '</span>件商品</span><span class="cart_handler_right">¥' + cartPrices + '</span>' +
                                '</div>' +
                                '<a href="{{ url('cart') }}" class="cart_go_btn" target="_blank">去购物车结算</a></div>';
                        $('.cart_num').html(cartNum);
                        $('.cart-panel .ibar_plugin_content').html(html);
                        $('.quick_links_pop').find('.loading-img').remove();

                    }).fail(function (jqXHR) {

                    }).always(function () {
                        $('.quick_links_pop').find('.loading-img').remove();
                    });
                } else {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            })
            //点击我的资产图标显示弹框
            $("#coupon-panel").click(function (e) {
                hideOtherTooltip();

                e.stopPropagation();
                flip = 0;
                if (couponFlip++ % 2 === 0) {
                    var coupon_head_html = '<a href="javascript:;" class="ibar_closebtn" title="关闭"><i class="fa fa-remove"></i></a>' +
                            '<div class="ibar_plugin_title">' +
                            '<h3>' +
                            '我的资产' +
                            '</h3>' +
                            '</div>' +
                            '<div class="pop_panel coupon-panel" >' +
                            ' <div class="ibar_plugin_content">' +
                            ' <div class="ia-head-list">' +
                            @if($user->type==cons('user.type.retailer'))
                                    '<a href="#" target="_blank" class="pl">' +
                            @else
                                    '<a href="{{ url('personal/coupon') }}" target="_blank" class="pl">' +
                            @endif

                                    '<div class="my-coupon-num">' +

                            ' </div>' +
                            ' <div class="text">' +
                            ' 优惠券' +
                            ' </div>' +
                            '</a>' +
                            '</div>' +

                            '<div class="ga-expiredsoon">' +
                            '<div class="es-head">' +
                            '即将过期优惠券' +
                            '</div>' +
                            '<div class="coupon-wrap my-coupon-wrap">' +
                            '<div class="coupon-loading-img" ><img src="../images/loading.gif"/>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +

                            @if(request()->is('shop/*') &&  $shop->id!=$user->shop->id)
                                    '<div class="ga-expiredsoon recevie-coupon-head">' +
                            '<div class="es-head">' +
                            '可领取优惠券' +
                            '</div>' +
                            '<div class="coupon-wrap my-recevie-coupon-wrap">' +
                            '<div class="coupon-loading-img"><img src="../images/loading.gif"/>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            @endif
                                    '</div>' +
                            '</div>' +
                            '<div class="arrow"><i></i></div>' +
                            '<div class="fix_bg"></div>';
                    $('.quick_links_pop').html(coupon_head_html);
                    $(".quick_links_pop").animate({left: -320, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1");

                    var html = '';
                    $.ajax({
                        url: site.api('coupon/user-coupon'),
                        method: 'get'
                    }).done(function (data) {
                        data = data.coupons;
                        $('.my-coupon-num').html(data.length);
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].diff_time == '') {
                                html += '<div class="coupon-panel bgc-blue">' +
                                        '<div class="validity">' +
                                        '<p>' +
                                        ' 有效期' +
                                        ' </p>' +
                                        ' <p>' +
                                        data[i].start_at +
                                        '</p>' +
                                        '<p>' +
                                        data[i].end_at +
                                        '</p>' +
                                        '</div>' +
                                        ' <ul>' +
                                        ' <li>' +
                                        ' <a href=" /shop/' + data[i].shop.id + '" target="_blank">' +
                                        data[i].shop.name +
                                        ' </a>' +
                                        ' </li>' +
                                        ' <li>' +
                                        ' ¥' + data[i].discount +
                                        '</li>' +
                                        '<li>' +
                                        '满' + data[i].full + '使用' +
                                        '</li>' +
                                        ' </ul>' +
                                        '</div>';
                            } else {
                                html += '<div class="coupon-panel bgc-red">' +
                                        ' <div class="expiration">' +
                                        '<span>' +
                                        data[i].diff_time + '后过期' +
                                        ' </span>' +
                                        ' </div>' +
                                        '  <ul>' +
                                        '<li>' +
                                        ' <a href=" /shop/' + data[i].shop.id + '" target="_blank">' +
                                        data[i].shop.name +
                                        '</a>' +
                                        '</li>' +
                                        ' <li>' +
                                        ' ¥' + data[i].discount +
                                        ' </li>' +
                                        '<li>' +
                                        ' 满' + data[i].full + '使用' +
                                        ' </li>' +
                                        ' </ul>' +
                                        '</div>';
                            }
                        }
                        $('.my-coupon-wrap').html(html);
                        $('.coupon-loading-img').remove();


                    }).fail(function () {

                    }).always(function () {
                        $('.coupon-loading-img').remove();
                    });
                    @if(request()->is('shop/*') &&  $shop->id!=$user->shop->id)
                        var shop = '{{ request()->is('shop/*') ? $shop->id : 0 }}'
                        var url = site.api('coupon/' + shop);
                        $.ajax({
                            url: url,
                            method: 'get'
                        }).done(function (data) {

                            var h = '';
                            data = data.coupons;

                            for (var i = 0; i < data.length; i++) {
                                h += '<div class="coupon-panel bgc-orange">' +
                                        '<div class="receive-wrap" data-id="' + data[i].id + '"><a class="not-receive">立即领取</a><a class="already-receive"><span' +
                                        ' class="fa fa-check"></span>已领</a></div>' +
                                        '<div class="validity"><p>有效期</p>' +

                                        '<p>' + data[i].start_at + '</p>' +

                                        '<p>' + data[i].end_at + '</p></div>' +
                                        '<ul>' +
                                        '<li>' + data[i].shop.name + '</li>' +
                                        '<li>¥' + data[i].discount + '</li>' +
                                        '<li>满' + data[i].full + '使用</li>' +
                                        '</ul>' +
                                        '</div>';

                            }
                            $('.recevie-coupon-head').find('.coupon-loading-img').remove();
                            $('.my-recevie-coupon-wrap').html(h);

                        }).always(function () {
                            $('.recevie-coupon-head').find('.coupon-loading-img').remove();
                        });
                    @endif

                } else {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            })
            //隐藏可领取优惠券提示
            function hideOtherTooltip() {
                $(".other_tooltip").hide();
            }

            function showOtherTooltip() {
                $(".other_tooltip").show();
            }

            //点击按钮关闭弹框
            $("#quick_links_pop").on("click", ".ibar_closebtn", function (e) {
                flip = 0;
                couponFlip = 0;
                $(".quick_links_pop").animate({left: -40, queue: true});
                $(".quick_links_pop").css("zIndex", "-1");
            });
            //用户头像显示信息
            $(".my_qlinks").mouseenter(function () {
                $(this).siblings(".ibar_login_box").show();
            })
            //点击其他地方隐藏div
            $(".my_qlinks").parent("li").mouseleave(function () {
                $(".ibar_login_box").hide();
            })
            $(document).click(function (e) {
                e = e || window.event;
                flip = 0;
                couponFlip = 0;
                if (e.target != $('.quick-wrap')[0] && e.target != $(".quick_links_pop")[0]) {
                    $(".quick_links_pop").animate({left: -40, queue: true});
                    $(".quick_links_pop").css("zIndex", "-1")
                }
            });
            $(".quick-wrap").click(function (event) {
                event.stopPropagation();
            });
            //滚动条离开顶部一定的高度 显示回到顶部按钮
            $(window).scroll(function () {
                if ($(window).scrollTop() > 100) {
                    $(".quick_toggle").addClass('quick_links_allow_gotop');
                } else {
                    $(".quick_toggle").removeClass('quick_links_allow_gotop');
                }
            })


            $(".hover_link").hover(function () {
                $(this).siblings('.mp_tooltip').css("visibility", 'inherit');
            }, function () {
                $(this).siblings('.mp_tooltip').css("visibility", 'hidden');
            })

            //点击回到顶部按钮
            $(".return_top").click(function () {
                $('html, body').animate({scrollTop: 0}, 'slow');
            });
            //领取优惠券
            $("#quick_links_pop").on("click", ".receive-wrap", function () {
                var coupon_id = $(this).data('id');
                var obj = $(this);

                $.ajax({
                    url: '/api/v1/coupon/receive/' + coupon_id,
                    data: {coupon: coupon_id},
                    method: 'post'
                }).done(function () {
                    obj.children(".not-receive").css("display", "none").siblings().css("display", "inline-block");
                    setTimeout(function () {
                        obj.css("display", "none")
                    }, 500)
                }).fail(function (jqXHR) {
                    var json = jqXHR['responseJSON'];
                    obj.closest('.coupon').find('.not-receive').css("display", "none").siblings().css("display", "inline-block").html('<span class="fa fa-remove"></span>' + json['message']);
                    setTimeout(function () {
                        obj.closest('.coupon').find('.not-receive').css("display", "inline-block").siblings().css("display", "none").html('<span class="fa fa-check"></span>已领');
                    }, 500)

                }).always(function () {

                });

            });
        });

    </script>
@stop