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
        data = {};
        targetUrl = $(this).data('url');
        var value = $('input[name="search_content"]').val();
        if (value == '') {
            return false;
        }
        data['search_content'] = value;
        _ajaxGet(targetUrl, data);
    });
    //待办事项动态加载详情
    $('a.ajax-get').on('click', function () {
        //修改选中状态
        $(this).addClass('btn-primary');
        $(this).siblings('a').removeClass('btn-primary');
        targetUrl = $(this).data('url');
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
    _ajaxGet(targetUrl, data)
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

            var str = '';

            $.each(list.data, function (index, result) {
                str += '<table class="table table-bordered">'
                    + '     <thead><tr><th>'
                    + '         <label><input type="checkbox" name="order_id[]" value="' + result.id + '"/>' + result.created_at + '</label>'
                    + '         <span class="order-number">订单号:' + result.id + '</span></th>'
                    + '         <th>' + (result.user_id == SITE.USER.id ? SITE.USER.user_name : result.shop.user.user_name) + '</th>'
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
                            str += '<p><a href="' + SITE.ROOT + '/order-buy/detail-' + (result.pay_type == 1 ? 'online' : 'cod') + '/' + result.id + '" class="btn btn-primary">查看</a></p>';
                            if (!result.is_cancel) {
                                if (result.pay_status == 0 && result.status == 1) {
                                    str += ' <p><a class="btn btn-cancel ajax" data-url="' + SITE.ROOT + '/order-sell/cancel-sure" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                }
                                if (result.pay_status == 0 && result.status != 0) {
                                    str += '<p><a href="#" class="btn btn-danger">付款</a></p>';
                                } else if (result.pay_type == 1 && result.status == 2) {
                                    str += '<p><a class="btn btn-danger ajax" data-url="' + SITE.ROOT + '/order-buy/batch-finish" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>已收货</a></p>';
                                }
                            }
                        } else {//卖家
                            str += '<p><a href="' + SITE.ROOT + '/order-sell/detail-' + (result.pay_type == 1 ? 'online' : 'cod') + '/' + result.id + '" class="btn btn-primary">查看</a></p>';
                            if (!result.is_cancel) {
                                if (result.status == 0) {
                                    str += '<p><a class="btn btn-danger ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/batch-sure" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>确认</a></p>';
                                } else if (result.pay_type == 1 && result.pay_status == 0 && result.status == 1) {
                                    str += '<p><a class="btn btn-cancel ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/cancel-sure" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                } else if ((result.pay_type == 1 && result.pay_status == 1 && result.status == 1) || (result.pay_type == 2 && result.status == 1)) {
                                    str += '<p><a class="btn btn-warning ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/batch-send" ' +
                                        'data-data=\'{"order_id"' + result.id + '}\'>发货</a></p>';
                                } else if (result.pay_type == 2 && result.pay_status == 1 && result.status == 2) {
                                    str += '<p><a class="btn btn-info ajax" data-method="put" data-url="' + SITE.ROOT + '/order-sell/batch-finish" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>收款</a></p>';
                                }
                                str += '<p><a href="#" class="btn btn-success">导出</a></p>';
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
        $('.dealer-top-header .city-list').css('display', 'block');
        $('.dealer-top-header .location-panel').css({'border': '1px solid #e0e0e0', 'border-bottom-color': '#fff'});
        $('.up-down').removeClass('fa-angle-down').addClass('fa-angle-up');
    })
    $('.dealer-top-header .city-wrap').mouseleave(function () {
        $('.dealer-top-header .city-list').css('display', 'none');
        $('.dealer-top-header .location-panel').css('border', '1px solid #f2f2f2');
        $('.up-down').removeClass('fa-angle-up').addClass('fa-angle-down');
    })

    $('.city-wrap .item a').on('click', function () {
        setCookie('province_id', $(this).data('id'));
        window.location.reload();
    })
    //city-menu end
    $('.collect-select').hover(function(){
        $(this).children(".collect-selected").siblings('.select-list').css('display','block');
        $(this).children(".collect-selected").children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        $(this).children(".collect-selected").css({'background': '#fff','border':'1px solid #e0e0e0','border-bottom':'none'});
    },function(){
        $(this).children(".collect-selected").siblings('.select-list').css('display','none');
        $(this).children(".collect-selected").children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        $(this).children(".collect-selected").css({'background': '#f2f2f2','border':'1px solid #f2f2f2'});
    })
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
        $(".banner-wrap .menu-down-wrap .menu-down-item").each(function () {
            if (titleIndex == $(this).index()) {
                $(".banner-wrap .menu-down-wrap .menu-down-layer:eq(" + $(this).index() + ")").css("display", "block").siblings().css("display", "none");
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
        $('.dealer-header .selected span').text($(this).text());
        $('.dealer-header .select-list').css('display', 'none');
    })
    //search role end

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
        ;
        ;
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
    $('.all-sort-panel .more').click(function () {
        if ($(this).children('span').text() == "更多") {
            $(this).siblings('.all-sort').css({'maxHeight': '100px', 'overflowY': 'scroll'});
            $(this).children('span').text('收起').siblings('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        } else if ($(this).children('span').text() == "收起") {
            $(this).siblings('.all-sort').css({'maxHeight': '55px', 'overflow': 'hidden'});
            $(this).children('span').text('更多').siblings('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        }

    })
}
