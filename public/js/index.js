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
    var offsetTop = obj.offset().top, offsetLeft = obj.offset().left, selfW = obj.outerWidth(true),
        w = tips.outerWidth(true), h = tips.outerHeight(true);
    tips.show().css({
        top: offsetTop - h + 10,
        left: (offsetLeft - (w - selfW) / 2)
    }).animate({top: offsetTop - h - 2}, function () {
        tips.fadeOut(1500);
    });
}

/**
 * 订单管理界面
 */
function getOrderButtonEvent() {
    $('.btn-cancel').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failOrderIds.length) {
            alert('取消失败的订单id : ' + textStatus.failOrderIds);
            $(this).button('reset');
            return false;
        }
    });
    $('.btn-receive').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failIds.length) {
            alert('确认失败的订单id : ' + textStatus.failIds);
            $(this).button('reset');
            return false;
        }
    });
    $('.btn-send').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failIds.length) {
            alert('发货失败的订单id : ' + textStatus.failIds);
            $(this).button('reset');
            return false;
        }
    });
    $('.export').click(function () {
        var obj = $(this), url = obj.data('url'), form = obj.closest('form');

        var orders = form.find('input.order_id:checked');
        if (!orders.length) {
            alert('请选择要导出的订单');
            return false;
        }
        var query = '';
        orders.each(function () {
            var orderId = $(this).val();
            query = query ? query + '&order_id[]=' + orderId : '?order_id[]=' + orderId;
        });
        window.location.href = url + query;
    })
}

/**
 * 加入购物车
 */
function joinCart() {
    // 判断登录
    if (!site.isLogin()) {
        site.redirect('auth/login');
        return;
    }
    $('.join-cart').on('click', function () {
        var obj = $(this), url = obj.data('url'),
            buyNum = obj.data('group') ? obj.siblings('input[name="num"]').val() : $('input[name="num"]').val();
        obj.button({
            loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
            doneText: '操作成功',
            failText: '操作失败'
        });
        obj.button('loading');
        $.ajax({
            url: url,
            method: 'post',
            data: {num: buyNum}
        }).done(function () {
            obj.button('done');
            $(".mask-outer").css("display", "block");
        }).fail(function (jqXHR) {
            obj.button('fail');
            var json = jqXHR['responseJSON'];
            if (json) {
                setTimeout(function () {
                    obj.html(json['message']);
                }, 0);
            }
        }).always(function () {
            setTimeout(function () {
                obj.button('reset');
            }, 2000);
        });

        return false;
    });
    $('a.close-btn,a.go-shopping').on('click', function () {
        $(".mask-outer").css("display", "none");
        // $('.add-to-cart').html('加入购物车');
        window.location.reload();
    });
}

/**
 * 购物车处理
 */
function cartFunc() {
    var shopCheckbox = $('.shop-checkbox'),
        goodsCheckbox = $('.goods-checkbox'),
        checkAll = $('.check-all'),
        checkFa = checkAll.children('.fa'),
        incButton = $('.inc-num'),
        descButton = $('.desc-num'),
        buyInput = incButton.siblings('.num');
    var initMoney = function () {
        var cartSumPriceSpan = $('.cart-sum-price'),
            cartSumPrice = 0,
            //submitBtn = $('input.btn-primary'),
            cartShops = $('.shopping-table-list table');
        cartShops.find('.parent-checkbox:checked').length === cartShops.find('.parent-checkbox').length ? checkFa.addClass('fa-check') : checkFa.removeClass('fa-check');
        cartShops.each(function () {
            var obj = $(this),
                shopSumPriceSpan = obj.find('.shop-sum-price'),
                shopSumPrice = 0,
                minMoney = obj.find('.min-money'),
                checkMinMoney = obj.find('.check-min-money');
            obj.find('.goods-list').each(function () {
                var tag = $(this),
                    goodsAllMonty = tag.find('.goods-all-money'),
                    descBtn = tag.find('.desc-num'),
                    buyNumInp = tag.find('.num'),
                    buyNum = parseInt(buyNumInp.val()),
                    minNum = buyNumInp.data('minNum');

                if (tag.find('.inp-checkbox').is(':checked')) {
                    var money = parseFloat(goodsAllMonty.html());
                    shopSumPrice = shopSumPrice.add(money);
                    cartSumPrice = cartSumPrice.add(money);
                }
                descBtn.prop('disabled', buyNum <= minNum);
            });
            shopSumPriceSpan.html(shopSumPrice);
            if (shopSumPrice < minMoney.html() && shopSumPrice) {
                checkMinMoney.html('不满足最低配送额￥').addClass('red');
                minMoney.addClass('red');
            } else {
                checkMinMoney.html('满足最低配送额￥').removeClass('red');
                minMoney.removeClass('red');
            }
        });

        /*   if ($('.not-enough:visible').length == 0 && cartSumPrice > 0) {
         submitBtn.prop('disabled', false);
         } else {
         submitBtn.prop('disabled', true);
         }*/

        cartSumPriceSpan.html(cartSumPrice);
    };
    //  添加和减少数量
    incButton.on('click', '', function () {
        var obj = $(this),
            buyInput = obj.siblings('.num'),
            minNum = buyInput.data('minNum'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money'),
            buyNum = parseInt(buyInput.val());
        if (buyNum < 20000) {
            buyInput.val(buyNum + 1);
            var goodsAllMoney = parseInt(buyInput.val()).mul(buyInput.data('price'));
            goodsAllMoneyTag.html(goodsAllMoney);
            initMoney();
        }
    });
    descButton.on('click', '', function () {
        var obj = $(this),
            buyInput = obj.siblings('.num'),
            minNum = buyInput.data('minNum'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money');
        buyInput.val(parseInt(buyInput.val()) - 1);

        var goodsAllMoney = parseInt(buyInput.val()).mul(buyInput.data('price'));
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
    });
    buyInput.on('keyup', '', function () {
        var obj = $(this),
            minNum = obj.data('min-num'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money'),
            buyNum = parseInt(obj.val());
        //if (buyNum > 20000) {
        //    obj.val(20000);
        //} else {
        if (buyNum < minNum) {
            obj.val(minNum);
            buyNum = minNum;
        } else if (buyNum > 20000) {
            obj.val(20000);
            buyNum = 20000;
        }
        var goodsAllMoney = buyNum.mul(obj.data('price'));
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
        //}

    });
    //checkBox选择
    shopCheckbox.click(function () {
        var obj = $(this),
            iconTag = obj.children('.fa'),
            checkbox = obj.next('input'),
            isChecked = iconTag.hasClass('fa-check'),
            childCheckbox = obj.closest('.table-bordered').find('.goods-checkbox');

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
            parentCheckbox = obj.closest('.table-bordered').find('.shop-checkbox');
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
        if (goodsCheckboxCount === goodsCheckedCount) {
            parentCheckbox.children('.fa').addClass('fa-check').end().next('input').prop('checked', true);
        } else {
            parentCheckbox.children('.fa').removeClass('fa-check').end().next('input').prop('checked', false);
        }
        initMoney()
    });
    checkAll.click(function () {
        var obj = $(this),
            iconTag = obj.children('.fa'),
            isChecked = iconTag.hasClass('fa-check');
        if (isChecked) {
            iconTag.removeClass('fa-check');
            shopCheckbox.children('.fa').removeClass('fa-check');
            shopCheckbox.next('input').prop('checked', false);
            goodsCheckbox.children('.fa').removeClass('fa-check');
            goodsCheckbox.next('input').prop('checked', false);
        } else {
            iconTag.addClass('fa-check');
            shopCheckbox.children('.fa').addClass('fa-check');
            shopCheckbox.next('input').prop('checked', true);
            goodsCheckbox.children('.fa').addClass('fa-check');
            goodsCheckbox.next('input').prop('checked', true);
        }
        initMoney();
    });
    initMoney();
}

/**
 * 加入购物车商品增加与减少
 */
var numChange = function () {
    "use strict"; // jshint ;_;

    // 初始化操作
    var cartGroup = {};
    $('.desc-num, .num, .inc-num').each(function () {
        var $this = $(this), group = $this.data('group') || 'default';
        cartGroup[group] = (cartGroup[group] || $()).add($this);
    });

    $.each(cartGroup, function (group, carts) {
        var descNum = carts.filter('.desc-num'),
            num = carts.filter('.num'),
            incNum = carts.filter('.inc-num'),
            minNum = num.data('minNum');

        if (descNum.length && num.length && incNum.length) {
            descNum.on('click', '', function () {
                num.val(parseInt(num.val()) - 1);
                changeDescButton();
            });
            incNum.on('click', '', function () {
                var buyNum = parseInt(num.val());
                if (buyNum < 20000) {
                    num.val(parseInt(num.val()) + 1);
                    changeDescButton();
                }
            });

            num.on('keyup', '', function () {
                var obj = $(this), buyNum = parseInt(obj.val());
                if (buyNum > 20000) {
                    obj.val(20000);
                } else if (buyNum < minNum) {
                    obj.val(minNum);
                } else {
                    changeDescButton();
                }
            });

            var changeDescButton = function () {
                if (num.val() <= minNum) {
                    descNum.prop('disabled', true);
                } else {
                    descNum.prop('disabled', false);
                }
            };
        }
    });
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
            status = self.children('.fa-star').length > 0,
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
                status = data.id;
            } else {
                status = null;
            }
            //self.data('status', status).button(status ? 'liked' : 'like');
            if ($('.like-' + type).length) {
                $('.like-' + type).data('status', status).button(status ? 'liked' : 'like');
            } else {
                self.data('status', status).button(status ? 'liked' : 'like');
            }

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


/**
 * 我的商品列表
 */
function myGoodsFunc() {

    var table = $(".goods-table-panel .goods-table");

    table.width($(".goods-table-panel .goods-table table").width());


    $('.mortgage, .gift').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
        mortgagedText: '设置成功'
    });

    /**
     * 抵费商品
     */
    $(document).on('click', '.mortgage, .gift', function () {
        $('body').append('<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>');
        var self = $(this),
            url = self.data('url'),
            method = self.data('method');

        self.button('loading');
        $.ajax({
            url: url,
            method: method,
            data: {}
        }).done(function (data, textStatus, jqXHR) {
            self.button('mortgaged').fadeOut(1500, function () {
                self.remove();
                $('body').find('.loading').remove();
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('body').find('.loading').remove();
            if (errorThrown == 'Unauthorized') {
                site.redirect('auth/login');
            } else {
                tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                self.button('reset');
            }
        });
    });

    ajaxNoForm();
    deleteNoForm();
}

/**
 * 无form删除
 */
var deleteNoForm = function () {
    $('.delete-no-form').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>'
    });
    /**
     * 商品删除
     */
    $(document).on('click', '.delete-no-form', function () {
        if (!confirm('真的要删除吗？')) {
            return false;
        }
        common.loading('show');
        var self = $(this),
            url = self.data('url'),
            method = self.data('method'),
            data = [];

        self.button('loading');

        $.each(self.data('data') || {}, function (name, value) {
            data.push({name: name, value: value});
        });
        $.ajax({
            url: url,
            method: method,
            data: data
        }).done(function (data, textStatus, jqXHR) {
            successMeg('删除成功');
            self.parents('tr').slideUp(function () {
                self.remove();
            })
        }).fail(function (jqXHR, textStatus, errorThrown) {
            if (errorThrown == 'Unauthorized') {
                site.redirect('auth/login');
            } else {
                tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                self.button('reset');
            }
        }).always(function () {
            common.loading('hide');
        });
    });
};

/**
 * 无form异步提交
 */
var ajaxNoForm = function () {
    var ajaxNoForm = $('.ajax-no-form');

    /*
     var setButton = function (onText, offText) {
     ajaxNoForm.button({
     loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
     onText: onText,
     offText: offText
     });
     }*/


    $(document).on('click', '.ajax-no-form', function () {
        var self = $(this),
            status = self.data('status'),
            url = self.data('url'),
            data = {status: status ? 0 : 1},
            onText = self.data('on'),
            offText = self.data('off'),
            changeStatus = self.data('changeStatus');
        self.button({
            loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
            onText: onText,
            offText: offText
        });

        // 序列化表单
        $.each(self.data('data') || {}, function (name, value) {
            data[name] = value
        });

        self.button('loading');
        $.ajax({
            url: url,
            method: 'put',
            data: data
        }).done(function (data, textStatus, jqXHR) {
            var statusName = self.closest('tr').find('.status-name');
            if ($.isPlainObject(data) && data.message) {
                self.data('status', 1).button('off');
                self.next('.delete').addClass('hidden');
                changeStatus ? statusName.html(onText.stripTags().trim()) : '';
            } else {
                self.data('status', 0).button('on');
                self.next('.delete').removeClass('hidden');
                changeStatus ? statusName.html(offText.stripTags().trim()) : '';
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            self.button(status ? 'off' : 'on');
            if (errorThrown == 'Unauthorized') {
                site.redirect('auth/login');
            } else {
                tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
            }
        });
    });
}

//--order-statistics--
/**
 * 统计页面js处理
 */
var statisticsFunc = function () {
    jQuery.browser = {};
    (function () {
        jQuery.browser.msie = false;
        jQuery.browser.version = 0;
        if (navigator.userAgent.match(/MSIE ([0-9]+)./)) {
            jQuery.browser.msie = true;
            jQuery.browser.version = RegExp.$1;
        }
    })();

    $(function () {
        var tableWidth = $(".table-scroll").parents("div").width();
        FixTable("MyTable1", 1, tableWidth, 300);
        FixTable("MyTable2", 1, tableWidth, 300);
        FixTable("MyTable3", 1, tableWidth, 300);
    })
}

/**
 * 添加商品图片处理
 */
var loadGoodsImages = function (barCode) {
    function LoadImg(barCode) {
        if (!barCode) {
            return false;
        }
        var url = url || site.api('my-goods/images');
        $.get(url, {'bar_code': barCode}, function (data) {
            var html = '', goodsImage = data['goodsImage'], goodsImageData = goodsImage['data'],
                imageBox = $('.goods-imgs'), uploadWrap = $('.image-upload');
            for (var index in goodsImageData) {
                html += '<div class="thumbnail col-xs-3">';
                html += '   <img alt="" src="' + goodsImageData[index]['image_url'] + '" data-id="' + goodsImageData[index]['id'] + '">';
                html += '</div>';
            }
            if (goodsImage['prev_page_url'] || goodsImage['next_page_url']) {
                html += '<ul class="pager col-xs-12">';
                if (goodsImage['prev_page_url'])
                    html += '     <li><a class="prev0 page" href="javascript:void(0)" data-url="' + goodsImage['prev_page_url'] + '">上一页</a></li>'
                if (goodsImage['next_page_url'])
                    html += '     <li><a class="next0 page"  href="javascript:void(0)" data-url="' + goodsImage['next_page_url'] + '">下一页</a></li>'
                html += '</ul>';
            }
            if (html) {
                imageBox.html(html);
                uploadWrap.addClass('hide').find('.pictures').html('');
            } else {
                imageBox.html('');
                uploadWrap.removeClass('hide');
            }
        }, 'json');
    }

    $('input[name="bar_code"]').on('blur', function () {
        var barCode = $(this).val();
        if (barCode.length >= 7) {
            LoadImg(barCode);
        } else {
            $('.goods-imgs').html('<div class="col-sm-12">条形码必须大于7位</div>');
        }

    });
    LoadImg(barCode);
};

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
 * 订单详情页修改单价功能事件
 */
function changePriceByDetailPage() {
    $('.change-price').click(function () {
        var obj = $(this),
            order_id = obj.data('id'),
            pivot_id = obj.data('pivot'),
            price = obj.data('price'),
            num = obj.data('num');
        $('input[name="order_id"]').val(order_id);
        $('input[name="pivot_id"]').val(pivot_id);
        price == 0 ? $('input[name="price"]').val(price).prop('disabled', true) : $('input[name="price"]').val(price).prop('disabled', false);
        $('input[name="num"]').val(num);
    });
    $(".see-more").click(function () {
        var self = $(this);
        self.siblings().children(".list-update").addClass("in");
        self.css("display", "none");
    })

}

/**
 * 订单打印方法
 */
function printFun() {
    window.print();
    setTimeout("window.close();", 0);
}




//店铺广告radio选择
function radioCheck() {
    $('.goodsIdRadio').click(function () {
        $('.goodsId').html('商品Id');
        $('.goodsidDiv').css('display', 'block');
        $('.promoteDiv').css('display', 'none');

    });
    $('.promoteRadio').click(function () {
        $('.goodsId').html('促销信息');
        $('.goodsidDiv').css('display', 'none');
        $('.promoteDiv').css('display', 'block');
    });

}

/**
 * 菜单设置
 */
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

    $('#one .item').on('click', function () {
        $('.city-one').text($(this).text());
        $(".check-city").css("display", "block");
        $("#myTab a:last").tab("show");
    })
    $('.city-one').on('click', function () {
        var city = $(".city-two").is(":visible");
        if (city) {
            $(".check-city").css("display", "none");
        }
    })
    //city-menu end

    //collect begin
    $('.collect-select').hover(function () {
        $(this).children('.collect-selected').addClass('active')
            .children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up').parents(".collect-selected")
            .siblings('.select-list').css('display', 'block');
    }, function () {
        $(this).children('.collect-selected').removeClass('active')
            .children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down').parents(".collect-selected")
            .siblings('.select-list').css('display', 'none');
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
        $(this).children('.menu-down-wrap').css('display', 'block').parents('li').siblings().children('.menu-down-wrap').css('display', 'none');
    })

    $('.categories-menu-item').mouseleave(function () {
        $('.categories .menu-wrap li').removeClass('hover-effect');
        $('.menu-down-layer').css('display', 'none');
        $('#menu-down-wrap .menu-down-layer').css('border', 'none');
        $('.categories-wrap .categories .menu-wrap').css('height', '0');
    })

    //店铺首页
    $('.shop-header-wrap .shop-name').mouseenter(function () {
        $('.shop-detail-popup').css("height", '350px');
    })
    $('.shop-header-wrap').mouseleave(function () {
        $('.shop-detail-popup').css("height", '0');
    })


    //top secondary-menu end
    $('.categories-wrap .categories .menu-wrap li').mouseenter(function () {
        $(this).addClass('hover-effect').siblings().removeClass('hover-effect');
        var titleIndex = $(this).index();
        $('.categories-wrap .menu-down-wrap .menu-down-item').each(function () {
            if (titleIndex == $(this).index()) {
                $('.categories-wrap .menu-down-wrap .menu-down-layer:eq(' + $(this).index() + ')').css('display', 'block').siblings().css('display', 'none');
            }
        })
    });

    var bannerHeight = $(".banner-slide .carousel-inner").height();
    $('#categories-wrap .menu-down-wrap .menu-down-layer').css('height', bannerHeight + "px");

    $('.categories-btn>a').mouseenter(function () {
        var bannerHeight = $(".banner-slide .carousel-inner ").height();
        $('.categories-wrap .categories .menu-wrap').css('height', "401px");
        $('#categories-wrap .categories .menu-wrap').css('height', bannerHeight + "px");
    })

    //search role begin
    $('.dealer-header .select-role').hover(function () {
        $(this).children('.select-list').css('display', 'block')
        $(this).children('.selected').children('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
    }, function () {
        $(this).children('.select-list').css('display', 'none')
        $(this).children('.selected').children('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
    });

    $('.dealer-header .select-list li a').click(function () {
        var obj = $(this), type = obj.data('type');
        $('input[name="type"]').remove();
        if (type) {
            obj.closest('form').attr('action', '/shop');
            $('input[name="name"]').after('<input type="hidden" name="type" value="' + type + '">');
        } else {
            obj.closest('form').attr('action', '/search');
        }
        obj.parent().addClass('hide').siblings().removeClass('hide');
        $('.dealer-header .selected span').text(obj.text());
        $('.dealer-header .select-list').css('display', 'none');
    })
    //search role end
    //left nav-menu
    $('.dealer-menu-list .list-item').click(function () {
        $(this).siblings('.menu-wrap').slideToggle();
    })

    //店铺 二维码
    $('.qr-code-wrap .code-show').mouseenter(function () {
        $('.qr-code-wrap .shop-code').addClass("shop-code-show");
    })
    $('.qr-code-wrap').mouseleave(function () {
        $('.shop-code').removeClass("shop-code-show");
    })

}
/**
 * 切换box
 */
function tabBox() {
    //$('.location').css('display', 'block')
    $('.switching a').click(function () {
        $(this).addClass('active').siblings().removeClass('active');
        var boxClass = $(this).attr('id');
        $('.' + boxClass).css('display', 'block').siblings('.box').css('display', 'none');
    })
}
/**
 * 商品显示与收起
 */
function displayList() {
    $(".sort-item-panel").each(function () {
        var height = $(this).children(".all-sort-panel").height();
        if (height > 60) {
            $(this).children(".all-sort-panel").children(".more").css('display', 'inline-block')
            $(this).children(".all-sort-panel").children(".all-sort").css({
                'max-height': '70px',
                'overflowY': 'hidden'
            });
        } else {
            $(this).children(".all-sort-panel").children(".more").css('display', 'none')
        }
    })

    $('.all-sort-panel .more').click(function () {
        var spanText = $(this).children('span').text();
        if (spanText == '更多') {
            $(this).siblings('.all-sort').css({'max-height': '100px', 'overflowY': 'scroll'});
            $(this).children('span').text('收起').siblings('.fa').removeClass('fa-angle-down').addClass('fa-angle-up');
        } else if (spanText == '收起') {
            $(this).siblings('.all-sort').css({'maxHeight': '60px', 'overflow': 'hidden'});
            $(this).children('span').text('更多').siblings('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
        }
    })
}
//设置地址
function setAddress(province, city, district, street) {
    var address = new Address(province, city, district, street);
}
//添加(编辑)商品验证
function validform() {

    var piecesLevel1 = $('select[name="pieces_level_1"]'),
        piecesLevel2 = $('select[name="pieces_level_2"]'),
        system1 = $('input[name="system_1"]'),
        system2 = $('input[name="system_2"]'),
        piecesLevel3 = $('select[name="pieces_level_3"]'),
        reg = new RegExp(/^[1-9]+[\d]*$/);

    $.validator.addMethod("images_check", function (value) {
        if (value != '' && !jQuery.isArray(value)) {
            return false;
        }
        return true;
    }, "必须是一个数组");
    $.validator.addMethod("pieces_level_2_check", function () {
        if (system1.val() != '' && piecesLevel2.val() == '') {
            return false;
        }
        return true;

    }, "二级单位必须选择");
    $.validator.addMethod("system_1_check", function () {
        if ((system2.val() != '' || piecesLevel3.val() != '') && !reg.test(system1.val())) {
            return false;
        }
        return true;
    }, "一级单位进制不合法");
    $.validator.addMethod("pieces_level_3_check", function () {
        if (piecesLevel3.val() == '' && system2.val() != '') {
            return false;
        }
        return true;
    }, "三级单位必须填写");
    $.validator.addMethod("system_2_check", function () {
        if (piecesLevel3.val() != '' && !reg.test(system2.val())) {
            return false;
        }
        return true;
    }, "二级单位进制不合法");
    $.validator.addMethod("pieces_level_2_same_check", function () {
        if (piecesLevel2.val() == piecesLevel1.val()) {
            return false;
        }
        return true;
    }, "二级单位不能与一级单位相同");
    $.validator.addMethod("pieces_level_3_same_check", function () {
        if (piecesLevel3.val() == piecesLevel2.val() || piecesLevel3.val() == piecesLevel1.val()) {
            return false;
        }
        return true;
    }, "三级单位不能与一级单位或二级单位相同");
    $.validator.addMethod('nameTrim', function () {
        if ($('input[name="name"]').val().trim() == "") {
            return false;
        }
        return true;
    }, '商品名称必须填写');
    return $("form").validate({
        debug: true,
        errorClass: 'red ajax-error',
        errorElement: 'span',
        rules: {
            name: {
                required: true,
                nameTrim: true
            },
            bar_code: {
                required: true,
                digits: true,
                minlength: 7,
                maxlength: 18
            },
            cate_level_1: {
                required: true,
                number: true,
                min: 0
            },
            cate_level_2: {
                required: true,
                number: true,
                min: 1
            },
            pieces_level_1: {
                required: true,
            },
            specification: {
                required: true,
            },
            images: {
                images_check: true
            },
            pieces_level_2: {
                pieces_level_2_check: true,
                pieces_level_2_same_check: true
            },
            system_1: {
                system_1_check: true
            },
            pieces_level_3: {
                pieces_level_3_check: true,
                pieces_level_2_same_check: true
            },
            system_2: {
                system_2_check: true
            }
        },
        messages: {
            name: {
                required: '商品名称必填填写',
            },
            bar_code: {
                required: '商品条形码必须填写',
                digits: '必须是数字',
                minlength: '至少7位',
                maxlength: '至多18位',
            },
            cate_level_1: {
                required: '一级分类必须选择',
                number: '必须是数字',
                min: '一级分类必须大于0'

            },
            cate_level_2: {
                required: '二级分类必须选择',
                number: '必须是数字',
                min: '二级分类必须大于1'

            },
            pieces_level_1: {
                required: '一级单位必须选择选择',
            },
            specification: {
                required: '最小单位规格必须填写',
            }
        }
    });
}
//选择单位变化时商品单位变化
function selectedChange() {
    //一级单位变化时
    $('select[name="pieces_level_1"]').change(function () {
        var retailerPiecesLevel1 = $('select[name="pieces_retailer"] .retailer_pieces_level_1'),
            wholesalerPiecesLevel1 = $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_1'),
            piecesRetailer = $('.pieces_retailer'),
            piecesWholesaler = $('.pieces_wholesaler'),
            piecesLevel1Select = $('select[name="pieces_level_1"] option:selected');

        if (retailerPiecesLevel1.length) {
            piecesRetailer.html() == retailerPiecesLevel1.text() && piecesRetailer.html(piecesLevel1Select.text());
            retailerPiecesLevel1.val($(this).val());
            retailerPiecesLevel1.text(piecesLevel1Select.text());
        } else {
            $('select[name="pieces_retailer"]').append('<option class="retailer_pieces_level_1" value="' + $(this).val() + '" >' + piecesLevel1Select.text() + '</option>');
        }
        if (wholesalerPiecesLevel1.length) {
            $('.pieces_wholesaler').html() == wholesalerPiecesLevel1.text() && piecesWholesaler.html(piecesLevel1Select.text());
            wholesalerPiecesLevel1.val($(this).val());
            wholesalerPiecesLevel1.text(piecesLevel1Select.text());
        } else {
            $('select[name="pieces_wholesaler"]').append('<option class="wholesaler_pieces_level_1" value="' + $(this).val() + '" >' + piecesLevel1Select.text() + '</option>');
        }
    });
    //二级单位变化时
    $('select[name="pieces_level_2"]').change(function () {
        if ($(this).find("option:selected").val() == '') {
            $('.system_1').html('');
            $('select[name="pieces_retailer"] .retailer_pieces_level_2').text() == $('.pieces_retailer').html() && $('.pieces_retailer').html('');
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').text() == $('.pieces_wholesaler').html() && $('.pieces_wholesaler').html('');
            $('select[name="pieces_retailer"] .retailer_pieces_level_2').length && $('select[name="pieces_retailer"] .retailer_pieces_level_2').remove();
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').length && $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').remove();
            return false;
        }
        $(this).find("option:selected").val() != '' && $('.system_1').html($(this).find("option:selected").text());
        if ($('select[name="pieces_retailer"] .retailer_pieces_level_2').length) {
            $('.pieces_retailer').html() == $('select[name="pieces_retailer"] .retailer_pieces_level_2').text() && $('.pieces_retailer').html($('select[name="pieces_level_2"] option:selected').text());
            $('select[name="pieces_retailer"] .retailer_pieces_level_2').val($(this).val());
            $('select[name="pieces_retailer"] .retailer_pieces_level_2').text($('select[name="pieces_level_2"] option:selected').text());
        } else {
            $('select[name="pieces_retailer"]').append('<option class="retailer_pieces_level_2" value="' + $(this).val() + '" >' + $('select[name="pieces_level_2"] option:selected').text() + '</option>');
        }
        if ($('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').length) {
            $('.pieces_wholesaler').html() == $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').text() && $('.pieces_wholesaler').html($('select[name="pieces_level_2"] option:selected').text());
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').val($(this).val());
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').text($('select[name="pieces_level_2"] option:selected').text());
        } else {
            $('select[name="pieces_wholesaler"]').append('<option class="wholesaler_pieces_level_2" value="' + $(this).val() + '" >' + $('select[name="pieces_level_2"] option:selected').text() + '</option>');
        }
    });
    //三级单位变化时
    $('select[name="pieces_level_3"]').change(function () {
        if ($(this).find("option:selected").val() == '') {
            $('.system_2').html('');
            $('select[name="pieces_retailer"] .retailer_pieces_level_3').text() == $('.pieces_retailer').html() && $('.pieces_retailer').html('');
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').text() == $('.pieces_wholesaler').html() && $('.pieces_wholesaler').html('');
            $('select[name="pieces_retailer"] .retailer_pieces_level_3').length && $('select[name="pieces_retailer"] .retailer_pieces_level_3').remove();
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').length && $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').remove();
            return false;
        }
        $(this).find("option:selected").val() != '' && $('.system_2').html($(this).find("option:selected").text());
        if ($('select[name="pieces_retailer"] .retailer_pieces_level_3').length) {
            $('.pieces_retailer').html() == $('select[name="pieces_retailer"] .retailer_pieces_level_2').text() && $('.pieces_retailer').html($('select[name="pieces_level_3"] option:selected').text());
            $('select[name="pieces_retailer"] .retailer_pieces_level_3').val($(this).val());
            $('select[name="pieces_retailer"] .retailer_pieces_level_3').text($('select[name="pieces_level_3"] option:selected').text());
        } else {
            $('select[name="pieces_retailer"]').append('<option class="retailer_pieces_level_3" value="' + $(this).val() + '" >' + $('select[name="pieces_level_3"] option:selected').text() + '</option>');
        }
        if ($('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').length) {
            $('.pieces_wholesaler').html() == $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_2').text() && $('.pieces_wholesaler').html($('select[name="pieces_level_3"] option:selected').text());
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').val($(this).val());
            $('select[name="pieces_wholesaler"] .wholesaler_pieces_level_3').text($('select[name="pieces_level_3"] option:selected').text());
        } else {
            $('select[name="pieces_wholesaler"]').append('<option class="wholesaler_pieces_level_3" value="' + $(this).val() + '" >' + $('select[name="pieces_level_3"] option:selected').text() + '</option>');
        }
    });
    //最小规格变化
    $('input[name="specification"]').change(function () {
        $('.spec').html($(this).val());
        var specification_retailer = $('input[name="specification_retailer"]').val(),
            specification_wholesaler = $('input[name="specification_wholesaler"]').val();
        $('input[name="specification_retailer"]').val(specification_retailer.substring(0, (specification_retailer.indexOf('*')) + 1) + $(this).val());
        $('input[name="specification_wholesaler"]').val(specification_wholesaler.substring(0, (specification_wholesaler.indexOf('*')) + 1) + $(this).val());
    });
    //促销信息的显示与隐藏
    $('input[name="is_promotion"]').change(function () {
        var promotionInfo = $('input[name="promotion_info"]');
        $(this).is(':checked') ? promotionInfo.prop('disabled', false).parents('.promotions-msg').removeClass('hide') : promotionInfo.prop('disabled', true).parents('.promotions-msg').addClass('hide');
    });

    $('select[name="pieces_retailer"]').change(function () {
        //1级单位
        system_1 = $('input[name = "system_1"]').val();
        //2级单位
        system_2 = $('div.system').find($('input[name = "system_2"]')).val();
        //最小规格单位
        specification = $('input[name="specification"]').val();
        var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
        $('.pieces_retailer').html(html);
        var value = $(this).find("option:selected").val();
        pieces = '';
        if (value == $('select[name="pieces_level_1"]').val()) {
            pieces = specification + (system_2 > 0 ? '*' + system_2 : '') + (system_1 > 0 ? '*' + system_1 : '')
        } else if (value == $('select[name="pieces_level_2"]').val()) {
            pieces = specification + (system_2 > 0 ? '*' + system_2 : '')

        } else if (value == $('select[name="pieces_level_3"]').val()) {
            pieces = specification

        }

        $('input[name="specification_retailer"]').val(pieces);
        $('div.spec_retailer').html(pieces)
    });

    $('select[name="pieces_wholesaler"]').change(function () {
        //1级单位
        system_1 = $('input[name = "system_1"]').val();
        //2级单位
        system_2 = $('div.system').find($('input[name = "system_2"]')).val();
        //最小规格单位
        specification = $('input[name="specification"]').val();
        var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
        $('.pieces_wholesaler').html(html);
        var value = $(this).find("option:selected").val();
        pieces = '';
        if (value == $('select[name="pieces_level_1"]').val()) {
            pieces = specification + (system_2 > 0 ? '*' + system_2 : '') + (system_1 > 0 ? '*' + system_1 : '')

        } else if (value == $('select[name="pieces_level_2"]').val()) {
            pieces = specification + (system_2 > 0 ? '*' + system_2 : '')

        } else if (value == $('select[name="pieces_level_3"]').val()) {
            pieces = specification
        }
        $('input[name="specification_wholesaler"]').val(pieces);
        $('div.spec_wholesaler').html(pieces)
    });

}
//购物车数据
function cartData() {
    $('#header_notification_bar').hover(function () {

        var cartDetail = $('.cart-detail');
        if (cartDetail.children('li').length) {
            return false;
        }

        $.ajax({
            url: site.api('cart/detail'),
            method: 'get'
        }).done(function (data) {
            var carts = data.carts, cartHtml = '';

            for (var i  in carts) {
                cartHtml += '<li>';
                cartHtml += '   <a href="/goods/' + carts[i].goods.id + '">';
                cartHtml += '       <span class="details clearfix">';
                cartHtml += '           <span class="label pull-left">';
                cartHtml += '               <img class="cart-img" src="' + carts[i].goods.image_url + '">';
                cartHtml += '           </span>';
                cartHtml += carts[i].goods.name;
                cartHtml += '       </span>';
                cartHtml += '   </a>';
                cartHtml += '</li>';
            }
            $('.cart-detail').html(cartHtml);

        });
    });
}

//签约管理
var signManage = function () {
    var expireModal = $('#expireModal'), signModal = $('#signModal');
    expireModal.on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget), type = parent.data('type'), id = parent.data('id');
        expireModal.find('input[name="type"]').val(type);
        expireModal.find('input[name="id"]').val(id);
    }).on('hide.bs.modal', function (e) {
        expireModal.find('.prompt').addClass('hide');
        expireModal.find('.qr-code').remove();
        expireModal.find('button[type="submit"]').prop('disabled', false).show();
    });

    signModal.on('hide.bs.modal', function (e) {
        signModal.find('.prompt').addClass('hide');
        signModal.find('.qr-code').remove();
        signModal.find('button[type="submit"]').prop('disabled', false).show();
    });

    var monthPanel = $(".month li"), monthInput = $('input[name="month"]');
    monthPanel.on('click', function () {
        var self = $(this), cost = self.data('cost'), month = self.data('month'), pieces = self.data('pieces');

        self.html(month + pieces).addClass("active").siblings().each(function () {
            $(this).removeClass("active").html($(this).html().replace('个月', ''));
        });
        monthInput.val(month);
        $(".xuqi-num").html("￥" + cost);
    }).first().click();

    $('.renew-form').on('done.hct.ajax', function (data, textStatus, jqXHR, self) {
        common.loading('hide');
        var qrcode = $('.qr-code-wrap .qr-code');
        if (!qrcode.length) {
            qrcode = $('<div class="qr-code" style="width: 256px; height: 256px; margin: 0 auto"></div>').appendTo('.qr-code-wrap');
            // qrcode = $('.qr-code-wrap .qr-code');
        }
        qrcode.qrcode(textStatus.code_url);
        $('button[type="submit"]').prop('disabled', true).hide();
        $('.prompt').removeClass('hide');
    })

    /*depositPay.on('click', function () {
     var self = $(this), url = site.api('personal/sign/deposit');
     self.button({
     loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
     doneText: '操作成功',
     failText: '操作失败'
     });

     self.button('loading')

     $.ajax({
     url: url,
     method: 'post'
     }).done(function () {
     site.redirect('personal/sign/deposit-pay');
     }).fail(function (error) {
     var json = error.responseJSON;
     setTimeout(function () {
     self.html(json['message']);
     }, 0);
     }).always(function () {
     setTimeout(function () {
     self.button('reset');
     }, 3000);
     });
     });*/
}

$(function () {
    menuFunc();
});


