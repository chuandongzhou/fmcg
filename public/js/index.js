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
    $('form').on('click', '.ajax-page', function () {
        var targetUrl = $(this).attr('data-url');
        _ajaxGet(targetUrl, {"page": $(this).attr('data-page')});
    });
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
                                    str += ' <p><a class="btn btn-cancel ajax" data-url="' + SITE.ROOT + '/api/v1/order/cancel-sure" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                }
                                if (result.pay_status == 0 && result.pay_type == 1) {
                                    str += '<p><a href="' + SITE.ROOT + '/pay/request/' + result.id + '" class="btn btn-danger">去付款</a></p>';
                                } else if (result.pay_type == 1 && result.status == 2) {
                                    str += '<p><a class="btn btn-danger ajax" data-url="' + SITE.ROOT + '/api/v1/order/batch-finish-of-buy" ' +
                                        'data-method="put" data-data=\'{"order_id":' + result.id + '}\'>确认收货</a></p>';
                                }
                            }
                        } else {//卖家
                            str += '<p><a href="' + SITE.ROOT + '/order-sell/detail?order_id' + result.id + '" class="btn btn-primary">查看</a></p>';
                            if (!result.is_cancel) {
                                if (result.pay_status == 0 && result.status == 1) {
                                    str += '<p><a class="btn btn-cancel ajax" data-method="put" data-url="' + SITE.ROOT + '/api/v1/order/cancel-sure" ' +
                                        'data-data=\'{"order_id":' + result.id + '}\'>取消</a></p>';
                                }
                                if ((result.pay_type == 1 && result.pay_status == 1 && result.status == 1) || (result.pay_type == 2 && result.status == 1)) {
                                    str += '<p><a class="btn btn-warning send-goods"  data-target="#sendModal" data-toggle="modal">发货</a></p>';
                                } else if (result.pay_type == 2 && result.status == 2) {
                                    str += '<p><a class="btn btn-info ajax" data-method="put" data-url="' + SITE.ROOT + '/api/v1/order/batch-finish-of-sell" ' +
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
            var totalPage = list.total / list.per_page;
            var pageHtml = '';
            if (totalPage > 1) {
                if (list.current_page == 1) {
                    pageHtml += '<li class="disabled"><span>&laquo;</span></li> ';
                } else {
                    pageHtml += '<li><a class="ajax-page" data-url="' + targetUrl + '" data-page="' + i + '" rel="prev">&laquo;</a></li>';
                }
                for (var i = 1; i <= totalPage; ++i) {
                    if (i == list.current_page) {
                        pageHtml += ' <li><a class="ajax-page active" data-url="' + targetUrl + '" data-page="' + i + '">' + i + '</a></li>';
                    } else {
                        pageHtml += ' <li><a class="ajax-page" data-url="' + targetUrl + '" data-page="' + i + '">' + i + '</a></li>';
                    }
                }
                if (list.current_page == list.total) {
                    pageHtml += '<li class="disabled"><span>&raquo;</span></li> ';
                } else {
                    pageHtml += ' <li><a class="ajax-page" data-url="' + targetUrl + '" data-page="' + i + '" rel="next">&raquo;</a></li>';
                }

            }
            $('.pagination').html(pageHtml);
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
        })
        .on('click', 'input[name="order_id[]"]', function () {
            $('#check-all').prop('checked', true);
            $('input[name="order_id[]"]').each(function () {
                if (!$(this).is(':checked')) {
                    $('#check-all').prop('checked', false);
                }
            });
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
    });

    $('.all-sort-panel .more').click(function () {
        if ($(this).children('span').text() == '更多') {
            $(this).siblings('.all-sort').css({'max-height': '100px', 'overflowY': 'scroll'});
            $(this).children('span').text('收起').siblings('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        } else if ($(this).children('span').text() == '收起') {
            $(this).siblings('.all-sort').css({'maxHeight': '60px', 'overflow': 'hidden'});
            $(this).children('span').text('更多').siblings('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        }
    });
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
 * 添加商品处理
 */
function addGoodsFunc(cate1, cate2, cate3) {
    var checkedLimit = 5, goodsImgsWrap = $('.goods-imgs');

    function loadImg(cate1, cate2, cate3) {
        var cate1 = cate1 || $('select[name="cate_level_1"]').val();
        var cate2 = cate2 || $('select[name="cate_level_2"]').val();
        var cate3 = cate3 || $('select[name="cate_level_3"]').val();

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
    });
    $('select.categories').change(function () {
        loadImg()
    });
    $('.attr').on('change', '.attrs', function () {
        loadImg()
    });
    //促销
    $('input[name="is_promotion"]').change(function () {
        var promotionInfo = $('textarea[name="promotion_info"]');
        $(this).val() == 1 ? promotionInfo.prop('disabled', false) : promotionInfo.prop('disabled', true);
    });
    loadImg(cate1, cate2, cate3);
}

/**
 *提现操作JS
 */
function getWithdraw(total_amount) {
    var bank = $('select[name="bank"] option:selected').val();
    var amount = 0;
    $('#withdraw').find('.btn-add').prop('disabled', true);
    $('input[name="amount"]').on('keyup', function () {
        amount = $(this).val();

        if (isNaN(amount)) {//不是数字
            $('.tip').show();
        } else if (amount > total_amount) {
            $('.tip').text('提现金额不合法').show();
        } else {
            $('.tip').hide();
            $('#withdraw').find('.btn-add').prop('disabled', false).attr('data-data', '{"amount":' + amount + ',"bank_id":' + bank + '}');
        }
    });
    $('#withdraw').on('change', 'select[name="bank"]', function () {
        bank = $(this).find('option:selected').val();
        if (bank) {
            $('#withdraw').find('.btn-add').attr('data-data', '{"amount":' + amount + ',"bank_id":' + bank + '}');
        } else {
            $('#withdraw').find('.btn-add').prop('disabled', false);
        }

    })
        //清空输入数据以及提示信息
        .on('hidden.bs.modal', function (e) {
            $('input[name="amount"]').val('');
            $('.tip').hide();
        });
}
/**
 * 动态显示提现进度
 */
function getWithdrawTimeItem() {
    $('.table').on('click', '.show-item', function () {
        var data = $.parseJSON($(this).attr('data-data'));
        var content = '<p>申请时间：' + data.created_at + '</p>';

        if (parseInt(data.pass_at) > 0) {
            content += '<p>审核通过时间：' + data.pass_at + '</p>';
        }
        if (parseInt(data.payment_at) > 0) {
            content += '<p>打款时间：' + data.payment_at + '</p>';
        }
        if (parseInt(data.failed_at) > 0) {
            content += '<p>审核不通过时间：' + data.failed_at + '</p>';
            content += '<p>审核不通过原因：' + data.reason + '</p>'
        }
        $('#withdraw-item').find('.text-left').html(content);
    });
}

/**
 * 订单详情页发货功能事件
 */
function sendGoodsByDetailPage() {
    $('#sendModal').find('.btn-add').click(function () {
        var order_id = $('.send-goods').attr('data-data');
        var delivery_man_id = $('select option:selected').val();
        $(this).attr('data-data', '{"order_id":' + order_id + ',"delivery_man_id":' + delivery_man_id + '}');
    });
}
/**
 * 订单详情页修改单价功能事件
 */
function changePriceByDetailPage() {
    $('#changePrice').find('.btn-add').prop('disabled', true);
    $('.change-price').click(function () {
        var order_id = $(this).attr('data-data');
        var pivot_id = $(this).attr('data-pivot');
        $('input[name="price"]').keyup(function () {
            var price = $(this).val();
            if (isNaN(price)) {//不是数字
                $('.tip').show();
            } else if (price < 0) {
                $('.tip').text('单价输入不合法').show();
            } else {
                $('.tip').hide();
                $('#changePrice').find('.btn-add').prop('disabled', false).attr('data-data', '{"price":' + price + ',"order_id":' + order_id + ',"pivot_id":' + pivot_id + '}');
            }
        });
    });
}
