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
        cartShops.find('.parent-checkbox:checked').length == cartShops.find('.parent-checkbox').length ? checkFa.addClass('fa-check') : checkFa.removeClass('fa-check');
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
        if (goodsCheckboxCount == goodsCheckedCount) {
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
                    ;
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

    //if ($(".goods-table-panel .goods-table tr").length > 7) {
    //    table.css({'height': '529px', 'overflowY': 'scroll'});
    //}

    $('.mortgage').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
        mortgagedText: '设置成功'
    });

    /**
     * 抵费商品
     */
    $(document).on('click', '.mortgage', function () {
        $('body').append('<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>');
        var self = $(this),
            url = self.data('url'),
            method = self.data('method');
        // 判断登录
        if (!site.isLogin()) {
            site.redirect('auth/login');
            return;
        }

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

    ajaxNoForm(true);
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
        $('body').append('<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>');
        var self = $(this),
            url = self.data('url'),
            method = self.data('method'),
            data = [];
        // 判断登录
        if (!site.isLogin()) {
            site.redirect('auth/login');
            return;
        }

        self.button('loading');

        $.each(self.data('data') || {}, function (name, value) {
            data.push({name: name, value: value});
        });
        $.ajax({
            url: url,
            method: method,
            data: data
        }).done(function (data, textStatus, jqXHR) {
            alert('删除成功');
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
            $('body').find('.loading').remove();
        });
    });
};

/**
 * 无form异步提交
 */
var ajaxNoForm = function (changeStatus) {
    var
        ajaxNoForm = $('.ajax-no-form'),
        onText = ajaxNoForm.data('on'),
        offText = ajaxNoForm.data('off');


    ajaxNoForm.button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
        onText: onText,
        offText: offText
    });
    $(document).on('click', '.ajax-no-form', function () {
        var self = $(this),
            status = self.data('status'),
            url = self.data('url'),
            data = {status: status ? 0 : 1};
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
            self.button(status ? 'up' : 'down');
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
            var html = '', goodsImage = data['goodsImage'], goodsImageData = goodsImage['data'], imageBox = $('.goods-imgs'), uploadWrap = $('.image-upload');
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
/**
 * 定位
 */
function setProvinceName() {
    var geolocation = new BMap.Geolocation();
    geolocation.getCurrentPosition(function (r) {
        if (this.getStatus() == BMAP_STATUS_SUCCESS) {
            setProvince(r.point.lng, r.point.lat);
        }
        else {
            alert('failed' + this.getStatus());
        }
    }, {enableHighAccuracy: true})

    function setProvince(lng, lat) {
        var myGeo = new BMap.Geocoder();
        myGeo.getLocation(new BMap.Point(lng, lat), function (result) {
            var provinceName = result.addressComponents.province;
            $('span.city-value').html(provinceName);
            $.post(site.api('address/province-id'), {name: provinceName}, function (data) {
                if (data.provinceId) {
                    setCookie('province_id', data.provinceId);
                }
            }, 'json')
        });
    }

}

/**
 * 方便的多次重复调用函数
 * @param {object} [options]
 * @param {number} [options.count] 总共重复的次数
 * @param {number} [options.delay] 重复间隔
 * @param {function} [options.tick] 重复时候的回调函数
 * @param {function} [options.done] 完成时的回调函数
 */
function timeIntervalFunc(options) {
    var count = options.count || 60, i = count, delay = options.delay || 1000,
        doneCallback = options.done || function () {
            },
        tickCallback = options.tick || function () {
            };

    var timer = setInterval(function () {
        // 继续
        if (--i > 0) {
            tickCallback(i);
            return;
        }

        // 完成
        doneCallback();
        clearInterval(timer);
    }, delay);
}

$(function () {
    menuFunc();
    //新消息提示框
    $(".msg-channel .close-btn").click(function () {
        $(this).closest('.msg-channel').animate({'bottom': '-160'});
    })
})

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
    })
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
    })

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

    //商品分类 begin
    //$('.sort-item .sort-list .list-title').mouseenter(function () {
    //    $(this).children('.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-up');
    //    $(this).addClass('active');
    //    $(this).siblings('.list-wrap').css('display', 'block');
    //    var height = $(this).siblings('.list-wrap').height();
    //    if (height > 350) {
    //        $(this).siblings('.list-wrap').addClass("scroll-height");
    //    }
    //})

    //$('.sort-item .sort-list').mouseleave(function () {
    //    $(this).children('.list-title').removeClass('active').children('.fa-angle-up').removeClass('fa-angle-up').addClass('fa-angle-down');
    //    $(this).children('.list-wrap').css('display', 'none').removeClass("scroll-height");
    //})
    //商品分类 end

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


function selectedFunc() {
    $('.span-checkbox').click(function () {
        var isCheck = $(this).siblings('.inp-checkbox').is(':checked');
        if (isCheck == false) {
            $(this).children(".fa").addClass('fa-check');
            $(this).siblings('.inp-checkbox').prop('checked', true);
        } else {
            $(this).children(".fa").removeClass('fa-check');
            $(this).siblings('.inp-checkbox').prop('checked', false);
        }
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

function showSuccessMeg(url) {
    $(".popup").css({"opacity": "1", "top": "20px"});
    setTimeout(function () {
        $(".popup").css({"opacity": "0", "top": "-150px"});
        if (url) {
            window.location.href = url;
        } else {
            location.reload();
        }
    }, 3000);
}
//设置地址
function setAddress(province, city, district, street) {

    var address = new Address(province, city, district, street);
}
//添加(编辑)商品验证
function validform() {
    $.validator.addMethod("images_check", function (value) {
        if (value != '' && !jQuery.isArray(value)) {
            return false;
        }
        return true;
    }, "必须是一个数组");
    $.validator.addMethod("pieces_level_2_check", function () {
        if ($('select[name="pieces_level_2"]').val() == '' && $('input[name="system_1"]').val() != '') {
            return false;
        }
        return true;

    }, "二级单位必须");
    $.validator.addMethod("system_1_check", function () {
        if (($('select[name="pieces_level_2"]').val() != '' || $('input[name="system_2"]').val() != '' || $('select[name="pieces_level_3"]').val() != '') && $('input[name="system_1"]').val() == '') {
            return false;
        }
        return true;
    }, "一级单位进制必须");
    $.validator.addMethod("pieces_level_3_check", function () {
        if ($('select[name="pieces_level_3"]').val() == '' && $('input[name="system_2"]').val() != '') {
            return false;
        }
        return true;
    }, "三级单位必须");
    $.validator.addMethod("system_2_check", function () {
        if ($('select[name="pieces_level_3"]').val() != '' && $('input[name="system_2"]').val() == '') {
            return false;
        }
        return true;
    }, "二级单位进制必须");
    $.validator.addMethod("pieces_level_2_same_check", function () {
        if ($('select[name="pieces_level_2"]').val() == $('select[name="pieces_level_1"]').val()) {
            return false;
        }
        return true;
    }, "二级单位不能与一级单位相同");
    $.validator.addMethod("pieces_level_3_same_check", function () {
        if ($('select[name="pieces_level_3"]').val() == $('select[name="pieces_level_2"]').val() || $('select[name="pieces_level_3"]').val() == $('select[name="pieces_level_1"]').val()) {
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
                maxlength: 18,
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
                required: '一级分类必须',
                number: '必须是数字',
                min: '一级分类必须大于0'

            },
            cate_level_2: {
                required: '二级分类必须',
                number: '必须是数字',
                min: '二级分类必须大于1'

            },
            pieces_level_1: {
                required: '一级单位必须选择',
            },
            specification: {
                required: '最小单位规格必须',
            }
        }
    });
}
//选择单位变化时商品单位变化
function selectedChange() {

    $('select[name="pieces_retailer"]').change(function () {

        var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
        $('.pieces_retailer').html(html);
        var value = $(this).find("option:selected").val();
        if (value == $('select[name="pieces_level_1"]').val()) {

            $('input[name="specification_retailer"]').val($('input[name="specification"]').val());

        } else if (value == $('select[name="pieces_level_2"]').val()) {
            $('input[name="specification_retailer"]').val($('input[name="system_1"]').val() + '*' + $('input[name="specification"]').val());

        } else if (value == $('select[name="pieces_level_3"]').val()) {
            $('input[name="specification_retailer"]').val($('input[name="system_1"]').val() * $('input[name="system_2"]').val() + '*' + $('input[name="specification"]').val());

        }
    });
    $('select[name="pieces_wholesaler"]').change(function () {
        var html = $(this).find("option:selected").text() == "请选择" ? '' : $(this).find("option:selected").text();
        $('.pieces_wholesaler').html(html);
        var value = $(this).find("option:selected").val();
        if (value == $('select[name="pieces_level_1"]').val()) {
            $('input[name="specification_wholesaler"]').val($('input[name="specification"]').val());

        } else if (value == $('select[name="pieces_level_2"]').val()) {
            $('input[name="specification_wholesaler"]').val($('input[name="system_1"]').val() + '*' + $('input[name="specification"]').val());

        } else if (value == $('select[name="pieces_level_3"]').val()) {
            $('input[name="specification_wholesaler"]').val($('input[name="system_1"]').val() * $('input[name="system_2"]').val() + '*' + $('input[name="specification"]').val());

        }
    });

}





