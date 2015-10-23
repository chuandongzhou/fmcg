/**
 * 显示提示
 * @param {object} obj
 * @param {string} content
 */
function tips(obj, content) {
    var tips = $('#tips');
    if (!tips.length) {
        tips = $('<div id="tips"><div class="text"></div><div class="arrow"></div></div>').appendTo($('body'));
    } else {
        tips.stop(true, true).hide().css({top: 0, left: 0});
    }
    tips.children('.text').html(content);
    var offsetTop = obj.offset().top, offsetLeft = obj.offset().left, selfW = obj.outerWidth(true), w = tips.outerWidth(true), h = tips.outerHeight(true);
    tips.show().css({
        top: offsetTop - h + 10,
        left: (offsetLeft - (w - selfW) / 2)
    }).animate({top: offsetTop - h - 2}, function () {
        tips.fadeOut(1500);
    });
}
/**
 * 动态查询订单列表
 */
function getOrderList() {
    //下拉列表方式
    $('.ajax-select').on('change', function () {
        _getArgs();
    });
    //时间查询方式
    $('#end-time').on('blur', function () {
        _getArgs();
    });
    //搜索按钮方式
    $('button.ajax-submit').on('click', function () {
        _getArgs();
    });
    //待办事项动态加载详情
    $('a.ajax-get').on('click', function () {
        //修改选中状态
        $(this).addClass('btn-primary');
        $(this).siblings('a').removeClass('btn-primary');
        targetUrl = $(this).data('url');
        //删除查询条件栏
        $('.pay-detail,.search').remove();
        _ajaxGet(targetUrl);
    });
}
/**
 * 获取需要查询的条件信息
 * @returns {boolean}
 * @private
 */
function _getArgs() {
    data = {};
    targetUrl = $('#target-url').val();
    //拼装查询的对象所属类型
    data['search_role'] = $('#search-role').val();

    //拼装select条件
    $('select.ajax-select').each(function () {
        var key = $(this).attr('name');
        data[key] = $(this).find('option:selected').val();

    });
    //拼装时间条件
    $('input.datetimepicker').each(function () {
        var key = $(this).attr('name');
        data[key] = $(this).val();
    });

    if (data['start_at'] > data['end_at']) {
        alert('开始时间不能晚于结束时间');
        return false;
    }
    var value = $('input[name="search_content"]').val();
    data['search_content'] = value;
    _ajaxGet(targetUrl, data);
}
/**
 * get方式，动态获取订单信息并显示到指定区域
 * @param targetUrl
 * @param data
 * @private
 */
function _ajaxGet(targetUrl, data) {
    $.ajax({
        type: 'get',
        url: targetUrl,
        data: data,
        dataType: 'json',
        success: function (list) {
            //重置全选按钮
            $('#check-all').prop('checked', false);
            if (list.data.length) {
                $('#foot-nav').show();
            } else {
                $('#foot-nav').hide();
            }

            var str = '';
            $.each(list.data, function (index, result) {

                str += '<table class="table table-bordered">'
                    + '     <thead><tr><th>'
                    + '         <label><input type="checkbox" name="order_id[]" value="' + result.id + '"/>' + result.created_at + '</label>'
                    + '         <span class="order-number">订单号:' + result.id + '</span></th>'
                    + '         <th>' + (result.user_id == SITE.USER.id ? result.shop.name : result.user.shop.name) + '</th>'
                    + '     <th></th><th></th><th></th>'
                    + '     </tr>'
                    + '         </thead><tbody>';
                $.each(result.goods, function (key, item) {
                    str += '            <tr><td>'
                        + '                 <img class="store-img" src="' + item.image_url + '">'
                        + '                 <a class="product-name" href="' + SITE.ROOT + '/goods/' + item.id + '">' + item.name + '</a>'
                        + '                 <td class="red">￥' + item.pivot.price + '</td>'
                        + '                 <td>' + item.pivot.num + '</td>';
                    if (0 == key) {
                        str += '         <td rowspan="' + result.goods.length + '" class="pay-detail text-center">'
                            + '         <p>订单状态 :' + result.status_name + '</p>'
                            + '         <p>支付方式 :' + result.payment_type + '</p>'
                            + '         <p>订单金额 :<span class="red">￥' + result.price + '</span></p>'
                            + '     </td>'
                            + '     <td rowspan="' + result.goods.length + '" class="operating text-center">';
                        if (SITE.USER.id == result.user_id) {//买家----需要修改参照order-buy/sell
                            str += '<p><a href="' + SITE.ROOT + '/order-buy/detail?order_id=' + result.id + '" class="btn btn-primary">查看</a></p>';
                            if (!result.is_cancel) {
                                if (result.pay_status == 0 && result.status == 1) {
                                    str += ' <p><a class="btn btn-cancel ajax" data-url="' + SITE.ROOT + '/order-sell/cancel-sure" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                }
                                if (result.pay_status == 0 && result.pay_type == 1) {
                                    str += '<p><a href="#" class="btn btn-danger">付款</a></p>';
                                } else if (result.pay_type == 1 && result.status == 2) {
                                    str += '<p><a class="btn btn-danger ajax" data-url="' + SITE.ROOT + '/order-buy/batch-finish" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>确认收货</a></p>';
                                }
                            }
                        } else {//卖家
                            str += '<p><a href="' + SITE.ROOT + '/order-sell/detail?order_id' + result.id + '" class="btn btn-primary">查看</a></p>';
                            if (!result.is_cancel) {
                                //if (result.status == 0) {
                                //    str += '<p><a class="btn btn-danger ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/batch-sure" ' +
                                //        'data-data=\'{"order_id":' + result.id + '}\'>确认</a></p>';
                                //}
                                if (result.pay_status == 0 && result.status == 1) {
                                    str += '<p><a class="btn btn-cancel ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/cancel-sure" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                }
                                if ((result.pay_type == 1 && result.pay_status == 1 && result.status == 1) || (result.pay_type == 2 && result.status == 1)) {
                                    str += '<p><a class="btn btn-warning send-goods"  data-target="#sendModal" data-toggle="modal">发货</a></p>';
                                } else if (result.pay_type == 2 && result.status == 2) {
                                    str += '<p><a class="btn btn-info ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/batch-finish" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>确认收款</a></p>';
                                }
                                if (result.status == 2) {
                                    str += '<p><a class="btn btn-success" href="' + SITE.ROOT + '/order-sell/export?order_id=' + result.id + '">导出</a></p>';
                                }
                            }
                        }
                        str += '             </td>';
                    }

                    str += '             </tr>';
                });

                str += '             </tbody></table>';

            });

            $('.content').html(str);

        }
    });
}
/**
 * 订单管理界面
 */
function getOrderButtonEvent() {
    $('.content')
        .on('click', '.send-goods', function () {
            $('input[name="order_id"]').val($(this).data('data'));
            $('.btn-close').click();
        })
        .on('click', 'input[name="order_id[]"]', function () {
            $('#check-all').prop('checked', true);
            $('input[name="order_id[]"]').each(function () {
                if (!$(this).is(':checked')) {
                    $('#check-all').prop('checked', false);
                }
            });
        })
    ;
    $('.btn-add').on('click', function () {
        $('.btn-close').click();
    });
    $('#check-all').on('click', function () {
        var orders = $('input[name="order_id[]"]');
        if ($(this).is(':checked')) {//选中
            orders.prop('checked', true);
        } else {//取消选中
            orders.prop('checked', false);
        }
    });
}
/*function tabBox() {
 $(".switching a").click(function () {
 $(this).addClass("active").siblings().removeClass("active");
 var boxclass = $(this).attr("id");
 $("." + boxclass).css("display", "block").siblings(".box").css("display", "none");
 })
 }*/
$(function () {
    menuFunc();
})
function menuFunc() {
    //city-menu begin
    $('.dealer-top-header .location-panel').mouseenter(function () {
        $('.dealer-top-header .city-list').css('display', 'block').siblings('.location-panel').addClass('selected-border');
        $('.up-down').removeClass('fa-angle-down').addClass('fa-angle-up');
    })

    $('.dealer-top-header .city-wrap').mouseleave(function () {
        $('.dealer-top-header .city-list').css('display', 'none').siblings('.location-panel').removeClass('selected-border');
        $('.up-down').removeClass('fa-angle-up').addClass('fa-angle-down');
    })

    $('.city-wrap .item').on('click', function () {
        var provinceId = $(this).children('a').data('id');
        setCookie('province_id', provinceId);
        window.location.reload();
    })
    //city-menu end

    //collect begin
    $('.collect-select').hover(function () {
        $(this).children('.collect-selected').siblings('.select-list').css('display', 'block');
        $(this).children('.collect-selected').children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        $(this).children('.collect-selected').addClass('active');
    }, function () {
        $(this).children('.collect-selected').siblings('.select-list').css('display', 'none');
        $(this).children('.collect-selected').children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        $(this).children('.collect-selected').removeClass('active');
    })
    //collect end

    //top secondary-menu begin
    $('.navbar-nav .menu-wrap-title').mouseenter(function () {
        $('.menu-list-wrap').css('display', 'block');
    })

    $('#menu-list').mouseleave(function () {
        $('.menu-list-wrap').css('display', 'none');
        $('.categories .menu-wrap li').removeClass('hover-effect');
        $('.menu-down-layer').css('display', 'none');
    })

    $('#menu-list .categories .menu-wrap li').mouseenter(function () {
        $(this).addClass('hover-effect').siblings().removeClass('hover-effect');
        $(this).children('.menu-down-wrap').css('display', 'block').parents('li').siblings().
            children('.menu-down-wrap').css('display', 'none');
        $(this).children('.menu-down-wrap').css('border', '1px solid #4cb9fe');
    })

    $('.categories-menu-item').mouseleave(function () {
        $('.categories .menu-wrap li').removeClass('hover-effect');
        $('.menu-down-layer').css('display', 'none');
        $('#menu-down-wrap .menu-down-layer').css('border', 'none');
    })
    //top secondary-menu end

    $('.banner-wrap .categories .menu-wrap li').mouseenter(function () {
        $(this).addClass('hover-effect').siblings().removeClass('hover-effect');
        var titleIndex = $(this).index();
        $('.banner-wrap .menu-down-wrap .menu-down-item').each(function () {
            if (titleIndex == $(this).index()) {
                $('.banner-wrap .menu-down-wrap .menu-down-layer:eq(' + $(this).index() + ')').css('display', 'block').siblings().css('display', 'none');
            }
        })
    })

    //search role begin
    $('.dealer-header .select-role').hover(function () {
        $(this).children('.select-list').css('display', 'block')
        $(this).children('.selected').children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
    }, function () {
        $(this).children('.select-list').css('display', 'none')
        $(this).children('.selected').children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
    })

    $('.dealer-header .select-list li a').click(function () {
        var obj = $(this), url = obj.data('url') || 'search';
        obj.closest('form').attr('action', url);
        $('.dealer-header .selected span').text(obj.text());
        $('.dealer-header .select-list').css('display', 'none');
    })
    //search role end

    //商品分类 begin
    $('.sort-item .sort-list .list-title').mouseenter(function () {
        $(this).children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        $(this).addClass('active');
        $(this).siblings('.list-wrap').css('display', 'block');
        var height = $(this).siblings('.list-wrap').height();
        if (height > 350) {
            $(this).siblings('.list-wrap').addClass("scroll-height");
        }

    })
    $('.sort-item .sort-list').mouseleave(function () {
        $(this).children('.list-title').removeClass('active').children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        $(this).children('.list-wrap').css('display', 'none').removeClass("scroll-height");
    })
    //商品分类 end

    //left nav-menu
    $('.dealer-menu-list .list-item').click(function () {
        $(this).siblings('.menu-wrap').slideToggle();
    })
}

function fixedBottom() {
    var scrolltop = document.documentElement.scrollTop + document.body.scrollTop;
    var bottom = $(document).height() - $(window).height() - $('.clearing-container').height();
    if (scrolltop > bottom) {
        $('.clearing-container').removeClass('fixed-bottom')
    } else {
        $('.clearing-container').addClass('fixed-bottom')
    }
}
function selectedFunc() {
    var initMoney = function () {
        var cartSumPriceSpan = $('.cart-sum-price'),
            cartSumPrice = 0,
            submitBtn = $('input.btn-primary');
        $('.shopping-table-list table').each(function () {
            var obj = $(this),
                shopCheckBox = obj.find('.shop-checkbox').next('input'),
                shopSumPriceSpan = obj.find('.shop-sum-price'),
                shopSumPrice = 0,
                minMoney = obj.find('.min-money'),
                notEnough = obj.find('.not-enough');
            obj.find('.goods-list').each(function () {
                var tag = $(this);
                if (tag.find('.inp-checkbox').is(':checked')) {
                    var money = parseInt(tag.find('.goods-all-money').html());
                    shopSumPrice += money;
                    cartSumPrice += money;
                }
            });
            shopSumPriceSpan.html(shopSumPrice);
            if (shopSumPrice < minMoney.html() && shopCheckBox.is(':checked')) {
                notEnough.removeClass('hidden');
            } else {
                notEnough.addClass('hidden');
            }
        });

        if ($('.not-enough:visible').length == 0 && cartSumPrice > 0) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }

        cartSumPriceSpan.html(cartSumPrice);
    };


    //  添加和减少数量
    var shopCheckbox = $('.shop-checkbox'),
        goodsCheckbox = $('.goods-checkbox'),
        incButton = $('.inc-num'),
        descButton = $('.desc-num'),
        buyInput = incButton.siblings('.num');
    incButton.on('click', '', function () {
        var obj = $(this),
            buyInput = obj.siblings('.num'),
            minNum = buyInput.data('minNum'),
            descButton = obj.siblings('.desc-num'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money');
        buyInput.val(parseInt(buyInput.val()) + 1);
        if (buyInput.val() <= minNum) {
            descButton.prop('disabled', true);
        } else {
            descButton.prop('disabled', false);
        }
        var goodsAllMoney = buyInput.val() * parseInt(buyInput.data('price'));
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
    });
    descButton.on('click', '', function () {
        var obj = $(this),
            buyInput = obj.siblings('.num'),
            minNum = buyInput.data('minNum'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money');
        buyInput.val(parseInt(buyInput.val()) - 1);
        if (buyInput.val() <= minNum) {
            obj.prop('disabled', true);
        } else {
            obj.prop('disabled', false);
        }
        var goodsAllMoney = buyInput.val() * parseInt(buyInput.data('price'));
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
    });
    buyInput.on('keyup', '', function () {
        var obj = $(this),
            minNum = obj.data('minNum'),
            descButton = obj.siblings('.desc-num'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money');
        if (obj.val() <= minNum) {
            descButton.prop('disabled', true);
        } else {
            descButton.prop('disabled', false);
        }
        var goodsAllMoney = obj.val() * parseInt(obj.data('price'));
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
    });

    /**
     * checkBox选择
     */
    shopCheckbox.click(function () {
        var obj = $(this),
            iconTag = obj.children('.fa'),
            checkbox = obj.next('input'),
            isChecked = iconTag.hasClass('fa-check'),
            childCheckbox = obj.closest('table').find('.goods-checkbox');

        if (isChecked) {
            iconTag.removeClass('fa-check');
            checkbox.prop('checked', false);
            childCheckbox.children('.fa').removeClass('fa-check').end().next('input').prop('checked', false);
        } else {
            iconTag.addClass('fa-check');
            checkbox.prop('checked', true);
            childCheckbox.children('.fa').addClass('fa-check').end().next('input').prop('checked', true);
        }
        initMoney();
    });
    goodsCheckbox.click(function () {
        var obj = $(this),
            iconTag = obj.children('.fa'),
            checkbox = obj.next('input'),
            isChecked = iconTag.hasClass('fa-check'),
            parentCheckbox = obj.closest('table').find('.shop-checkbox');
        if (isChecked) {
            iconTag.removeClass('fa-check');
            checkbox.prop('checked', false);
        } else {
            iconTag.addClass('fa-check');
            checkbox.prop('checked', true);
        }
        var tableNode = obj.closest('tbody'),
            goodsCheckboxCount = tableNode.find('.goods-checkbox').length,
            goodsCheckedCount = tableNode.find('input[type="checkbox"]:checked').length;
        if (goodsCheckboxCount == goodsCheckedCount) {
            parentCheckbox.children('.fa').addClass('fa-check').end().next('input').prop('checked', true);
        } else {
            parentCheckbox.children('.fa').removeClass('fa-check').end().next('input').prop('checked', false);
        }
        initMoney()
    });
    initMoney();
}


var numChange = function (num) {
    var incButton = $('.inc-num'),
        descButton = $('.desc-num'),
        buyInput = incButton.siblings('.num'),
        cartBtn = $('.add-to-cart');
    var changeDescButton = function () {
        if (buyInput.val() <= num) {
            descButton.prop('disabled', true);
        } else {
            descButton.prop('disabled', false);
        }
        cartBtn.data('data', {num: buyInput.val()});
    };
    incButton.on('click', '', function () {
        buyInput.val(parseInt(buyInput.val()) + 1);
        changeDescButton();
    });
    descButton.on('click', '', function () {
        buyInput.val(parseInt(buyInput.val()) - 1);
        changeDescButton();
    });
    buyInput.on('keyup', '', function () {
        changeDescButton();
    });
    changeDescButton();
};
/**
 * 点击感兴趣处理函数
 * @param {string} module         模块
 * @returns {undefined}
 */
function likeFunc() {
    $('.btn-like').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中',
        likedText: '<i class="fa fa-star"></i> 已收藏',
        likeText: '<i class="fa fa-star-o"></i> 加入收藏夹'
    });

    $(document).on('click', '.btn-like', function () {
        var self = $(this),
            id = self.data('id'),
            status = self.children('.fa-star').length > 0
        type = self.data('type') || 'goods';
        // 判断登录
        if (!site.isLogin()) {
            site.redirect('auth/login');
            return;
        }

        self.button('loading');
        $.ajax({
            url: site.api('like/interests'),
            method: 'put',
            data: {status: !status ? 1 : 0, type: type, id: id}
        }).done(function (data, textStatus, jqXHR) {
            if ($.isPlainObject(data)) {
                status = data.message.id;
            } else {
                status = null;
            }
            self.data('status', status).button(status ? 'liked' : 'like');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            self.button(status ? 'liked' : 'like');
            if (errorThrown == 'Unauthorized') {
                site.redirect('auth/login');
            } else {
                tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
            }
        });
    });
}

function tabBox() {
    $('.location').css('display', 'block')
    $('.switching a').click(function () {
        $(this).addClass('active').siblings().removeClass('active');
        var boxclass = $(this).attr('id');
        $('.' + boxclass).css('display', 'block').siblings('.box').css('display', 'none');
    })
}


function displayList() {
    $(".sort-item-panel").each(function () {
        var height = $(this).children(".all-sort-panel").height();
        if (height > 60) {

            $(this).children(".all-sort-panel").children(".more").css('display', 'inline-block')
            $(this).children(".all-sort-panel").children(".all-sort").css({
                'max-height': '60px',
                'overflowY': 'hidden'
            });
        } else {
            $(this).children(".all-sort-panel").children(".more").css('display', 'none')
        }
    })

    $('.all-sort-panel .more').click(function () {
        if ($(this).children('span').text() == '更多') {
            $(this).siblings('.all-sort').css({'max-height': '100px', 'overflowY': 'scroll'});
            $(this).children('span').text('收起').siblings('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        } else if ($(this).children('span').text() == '收起') {
            $(this).siblings('.all-sort').css({'maxHeight': '60px', 'overflow': 'hidden'});
            $(this).children('span').text('更多').siblings('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        }
    })
}

}

//--order-statistics--
/**
 * 统计页面js处理
 */
function statisticsFunc() {
    $('.span-checkbox').click(function () {
        var inp_checkbox = $(this).siblings('.inp-checkbox');
        var isCheck = inp_checkbox.is(':checked');
        var fa = $(this).children('.fa');
        if (isCheck == false) {
            fa.addClass('fa-check');
            inp_checkbox.prop('checked', true);
            $('.checkbox-flag').val(1);
        } else {
            fa.removeClass('fa-check');
            inp_checkbox.prop('checked', false);
            $('.checkbox-flag').val(0);
        }
        //提交表单
        $('form').submit();
    });

    $('#export').click(function () {
        var oldUrl = $('form').attr('action');
        var newUrl = '{{ url("order/stat-export") }}';
        $('form').attr('action', newUrl).attr('method', 'post').submit();
        //初始化设置
        $('form').attr('action', oldUrl).attr('method', 'get');

    });

    var target = $('input[name="order_page_num"]');
    var target_value = parseInt(target.attr('value'));
    $('.next0,.next1').on('click', function () {
        target.attr('value', target_value + 1);
        $('form').submit();
    });

    $('.prev0,.prev1').on('click', function () {
        target.attr('value', target_value - 1);
        $('form').submit();
    });
}

/**
 * address页面百度地图相关Js
 */
function baiDuMap() {
    //初始化这个变量,防止百度地图重复实例化所导致的显示错误问题
    var flag = false;
    if (!flag) {
        var map_modal = new BMap.Map("map-modal");
    }
    var point_modal = new BMap.Point(106, 35);
    map_modal.centerAndZoom(point_modal, 12);

    //添加地址点击时载入当前位置的地图
    $('#add-address').click(function () {
        if (!flag) {
            //默认定位到当前浏览器位置
            var geolocation_modal = new BMap.Geolocation();
            geolocation_modal.getCurrentPosition(function (r) {
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    //重置中心点
                    map_modal.addOverlay(new BMap.Marker(r.point));
                    map_modal.panTo(r.point);
                }
            }, {enableHighAccuracy: true});
            flag = true;
        }
    });

    $('.address-select select').on('change', function () {
        var elem = $(this);
        if (elem.val()) {
            var num = 6;
            if (elem.hasClass('address-city')) {
                num = 12;
            }
            if (elem.hasClass('address-district')) {
                num = 14;
            }
            if (elem.hasClass('address-street')) {
                num = 16;
            }
            var areaName = elem.find('option:checked').text();
            if (areaName != '其它区' && areaName!='海外') {
                //删除之前的覆盖物
                map_modal.clearOverlays();
                // 创建地址解析器实例
                var myGeo = new BMap.Geocoder();
                // 将地址解析结果显示在地图上,并调整地图视野
                myGeo.getPoint(areaName, function (newPoint) {
                    if (newPoint) {
                        point_modal = newPoint;
                        map_modal.centerAndZoom(newPoint, num);

                        //重置中心点
                        map_modal.addOverlay(new BMap.Marker(newPoint));
                        // 设置矩形区域
                        var stepLang = 0.01;
                        if (elem.hasClass('address-street')) {
                            stepLang = 0.005;
                        }
                        if (!elem.hasClass('address-province')) {
                            polygon_modal = new BMap.Polygon([
                                new BMap.Point(parseFloat(point_modal.lng - stepLang), parseFloat(point_modal.lat + stepLang)),
                                new BMap.Point(parseFloat(point_modal.lng + stepLang), parseFloat(point_modal.lat + stepLang)),
                                new BMap.Point(parseFloat(point_modal.lng + stepLang), parseFloat(point_modal.lat - stepLang)),
                                new BMap.Point(parseFloat(point_modal.lng - stepLang), parseFloat(point_modal.lat - stepLang))
                            ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5, fillOpacity: 0.1});  //创建多边形
                            map_modal.addOverlay(polygon_modal);   //将图形添加到地图
                            var coordinate = polygon_modal.getBounds();
                            $('input[name="coordinate_blx"]').val(coordinate.bl.lng);
                            $('input[name="coordinate_bly"]').val(coordinate.bl.lat);
                            $('input[name="coordinate_slx"]').val(coordinate.sl.lng);
                            $('input[name="coordinate_sly"]').val(coordinate.sl.lat);
                            polygon_modal.addEventListener('lineupdate', function () {
                                coordinate = polygon_modal.getBounds();
                                $('input[name="coordinate_blx"]').val(coordinate.bl.lng);
                                $('input[name="coordinate_bly"]').val(coordinate.bl.lat);
                                $('input[name="coordinate_slx"]').val(coordinate.sl.lng);
                                $('input[name="coordinate_sly"]').val(coordinate.sl.lat);
                            });
                        }
                    }
                }, areaName);

            }
        }

    });
}

function dynamicShowMap() {
    map.clearOverlays();
    $('.show-map').each(function () {
        var blx = $(this).find('input[name="area[blx][]"]').val();
        var bly = $(this).find('input[name="area[bly][]"]').val();
        var slx = $(this).find('input[name="area[slx][]"]').val();
        var sly = $(this).find('input[name="area[sly][]"]').val();
        var point_lng = parseFloat(slx) + (blx - slx) / 2;
        var point_lat = parseFloat(sly) + (bly - sly) / 2;
        var point = new BMap.Point(point_lng, point_lat);
        var marker = new BMap.Marker(point);  // 创建标注
        map.addOverlay(marker);               // 将标注添加到地图中
        marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

        var polygon = new BMap.Polygon([
            new BMap.Point(slx, bly),//左上
            new BMap.Point(blx, bly),//右上
            new BMap.Point(blx, sly),//右下
            new BMap.Point(slx, sly)//左下

        ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5, fillOpacity: 0.1});  //创建多边形
        map.addOverlay(polygon);   //将图形添加到地图
    });
}
/**
 * 根据配送区域显示相应地图
 *
 * @param data
 */
function getCoordinateMap(data) {
    map = new BMap.Map("map");
    if(data && data.length){
        $.each(data, function (index, value) {
            var point_lng = parseFloat(value['coordinate']['sl_lng']) + (value['coordinate']['bl_lng'] - value['coordinate']['sl_lng']) / 2;
            var point_lat = parseFloat(value['coordinate']['sl_lat']) + (value['coordinate']['bl_lat'] - value['coordinate']['sl_lat']) / 2;
            var point = new BMap.Point(point_lng, point_lat);
            if (!index) {
                //map.centerAndZoom(point,5);//取第一个中心点为地图默认中心
                map.centerAndZoom(new BMap.Point(106, 35), 5);
            }
            var marker = new BMap.Marker(point);  // 创建标注
            map.addOverlay(marker);               // 将标注添加到地图中
            marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

            var polygon = new BMap.Polygon([
                new BMap.Point(value['coordinate']['sl_lng'], value['coordinate']['bl_lat']),//左上
                new BMap.Point(value['coordinate']['bl_lng'], value['coordinate']['bl_lat']),//右上
                new BMap.Point(value['coordinate']['bl_lng'], value['coordinate']['sl_lat']),//右下
                new BMap.Point(value['coordinate']['sl_lng'], value['coordinate']['sl_lat'])//左下

            ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5, fillOpacity: 0.1});  //创建多边形
            map.addOverlay(polygon);   //将图形添加到地图
        });
    }else{
        map.centerAndZoom(new BMap.Point(106, 35), 5);

    }
}

function getShopAddressMap(lng,lat){
    var addressMap = new BMap.Map('address-map');
    if(lng && lat){
        var point_address = new BMap.Point(lng, lat);
        addressMap.centerAndZoom(point_address, 12);
        addressMap.addOverlay(new BMap.Marker(point_address));
    }else{
        var point_address = new BMap.Point(106, 35);
        addressMap.centerAndZoom(point_address, 12);
        var geolocation_address = new BMap.Geolocation();
        geolocation_address.getCurrentPosition(function (r) {
            if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                //重置中心点
                addressMap.addOverlay(new BMap.Marker(r.point));
                addressMap.panTo(r.point);
            }
        }, {enableHighAccuracy: true});
    }
    $('.shop-address').on('change','select',function(){
        var elem = $(this);
        var areaName = elem.find('option:checked').text();
        var num = 6;
        if (elem.hasClass('address-city')) {
            num = 12;
        }
        if (elem.hasClass('address-district')) {
            num = 14;
        }
        if (elem.hasClass('address-street')) {
            num = 16;
        }
        if (areaName != '其它区' && areaName!='海外') {
            //删除之前的覆盖物
            addressMap.clearOverlays();
            // 创建地址解析器实例
            var myGeo = new BMap.Geocoder();
            // 将地址解析结果显示在地图上,并调整地图视野
            myGeo.getPoint(areaName, function (newPoint) {
                if (newPoint) {
                    point_address = newPoint;
                    addressMap.centerAndZoom(newPoint, num);
                    var newMarker = new BMap.Marker(point_address);
                    //重置中心点
                    addressMap.addOverlay(newMarker);
                    var pointPosition = newMarker.getPosition();
                    $('input[name="x_lng"]').val(pointPosition.lng);
                    $('input[name="y_lat"]').val(pointPosition.lat);
                    newMarker.enableDragging();//可拖拽点
                    newMarker.addEventListener('dragend',function(){
                        pointPosition = newMarker.getPosition();
                        $('input[name="x_lng"]').val(pointPosition.lng);
                        $('input[name="y_lat"]').val(pointPosition.lat);
                    });
                }
            }, areaName);

        }
    });
}
/**
 * 添加商品处理
 */
function addGoodsFunc(cate1 , cate2, cate3) {
    var checkedLimit = 5, goodsImgsWrap = $('.goods-imgs');
   function loadImg(cate1 , cate2, cate3) {
        var cate1 = cate1||  $('select[name="cate_level_1"]').val();
        var cate2 = cate2||  $('select[name="cate_level_2"]').val();
        var cate3 = cate3||  $('select[name="cate_level_3"]').val();

        var attrs = $('.attrs');
        var array = new Array(); //定义数组
        attrs.each(function () {
            var val = $(this).val();
            if (val > 0)
                array.push(val);
        });
        var data = {
            'cate_level_1': cate1,
            'cate_level_2': cate2,
            'cate_level_3': cate3,
            'attrs': array
        };
        $.get(site.api('my-goods/images'), data, function (data) {
            var html = '', goodsImage = data['goodsImage'], imageBox = $('.load-img-wrap');
            for (var index in goodsImage) {
                html += '<div class="thumbnail col-xs-3 img-' + goodsImage[index]['id'] + '">';
                html += '   <img alt="" src="' + goodsImage[index]['image_url'] + '" data-id="' + goodsImage[index]['id'] + '">';
                html += '</div>';
            }

            imageBox.html(html);
        }, 'json');
    }
    $('.load-img-wrap').on('click', '.thumbnail', function () {

        if (goodsImgsWrap.children('.thumbnail').length >= checkedLimit) {
            return false;
        }
        var obj = $(this).children('img'), imgSrc = obj.attr('src'), imgId = obj.data('id');
        var str = '<div class="thumbnail col-xs-3">';
        str += '<button aria-label="Close" class="close" type="button">';
        str += '    <span aria-hidden="true">×</span>';
        str += '</button>';
        str += '<img alt="" src="' + imgSrc + '">';
        str += '<input type="hidden" value="' + imgId + '" name="images[]">';
        str += '</div>';
        goodsImgsWrap.append(str);
        $(this).addClass('hidden');
    });
    goodsImgsWrap.on('click', '.close', function () {
        var parents = $(this).closest('.thumbnail'), imageId = parents.find('input:hidden').val();
        parents.fadeOut(500, function () {
            $(this).remove();
            $('.img-' + imageId).removeClass('hidden');
        })
    })
    $('select.categories').change(function () {
        loadImg()
    });
    $('.attr').on('change' , '.attrs' , function () {
        loadImg()
    });
    //促销
    $('input[name="is_promotion"]').change(function () {
        var promotionInfo = $('textarea[name="promotion_info"]');
        $(this).val() == 1 ? promotionInfo.prop('disabled', false) : promotionInfo.prop('disabled', true);
    });
    loadImg(cate1 , cate2, cate3);
}