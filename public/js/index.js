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
    $('.content')
        .on('click', '.send-goods', function () {
            $('input[name="order_id"]').val($(this).data('data'));
        })
        .on('click', 'input[name="order_id[]"]', function () {
            var child = $('input[name="order_id[]"]');
            $('#check-all').prop('checked', child.length === child.filter(':checked').length);
        });
    $('#check-all').on('click', function () {
        $('input[name="order_id[]"]').prop('checked', $(this).prop('checked'));
    });
    $('.btn-cancel').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failOrderIds.length) {
            alert('取消失败的订单id : ' + textStatus.failOrderIds);
            site.refresh(true);
        }
    });
    $('.btn-receive').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failIds.length) {
            alert('确认失败的订单id : ' + textStatus.failIds);
        }
    });
    $('.btn-send').on('done.ajax.hct', function (data, textStatus, jqXHR, self) {
        if (textStatus.failIds.length) {
            alert('发货失败的订单id : ' + textStatus.failIds);
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
/*function tabBox() {
 $(".switching a").click(function () {
 $(this).addClass("active").siblings().removeClass("active");
 var boxclass = $(this).attr("id");
 $("." + boxclass).css("display", "block").siblings(".box").css("display", "none");
 })
 }*/
$(function () {
    menuFunc();
    //新消息提示框
    //$(".msg-channel").css("bottom", "5px");
    $(".msg-channel .close-btn").click(function () {
        $(this).closest('.msg-channel').css('display', 'none');
    })
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
        var obj = $(this), type = obj.data('type');
        $('input[name="type"]').remove();
        if (type) {
            obj.closest('form').attr('action', 'shop');
            $('input[name="name"]').after('<input type="hidden" name="type" value="' + type + '">');
        } else {
            obj.closest('form').attr('action', 'search');
        }
        obj.parent().addClass('hide').siblings().removeClass('hide');
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
                    var money = parseFloat(tag.find('.goods-all-money').html());
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
        var goodsAllMoney = buyInput.val() * (buyInput.data('price') * 100) / 100;
        goodsAllMoneyTag.html(goodsAllMoney);
        initMoney();
    });
    descButton.on('click', '', function () {
        var obj = $(this),
            buyInput = obj.siblings('.num'),
            minNum = buyInput.data('minNum'),
            goodsAllMoneyTag = obj.closest('tr').find('.goods-all-money');
        if (buyInput.val() <= minNum) {
            obj.prop('disabled', true);
        } else {
            buyInput.val(parseInt(buyInput.val()) - 1);
            obj.prop('disabled', false);
        }
        var goodsAllMoney = buyInput.val() * (buyInput.data('price') * 100) / 100;
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
        var goodsAllMoney = obj.val() * (buyInput.data('price') * 100) / 100;
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

/**
 *
 * @param num
 */
var numChange = function (num) {
    var incButton = $('.inc-num'),
        descButton = $('.desc-num'),
        buyInput = incButton.siblings('.num');
    var changeDescButton = function () {
        if (buyInput.val() <= num) {
            descButton.prop('disabled', true);
        } else {
            descButton.prop('disabled', false);
        }
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
                status = data.id;
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

/**
 * 商品显示与收起
 */
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
/**
 * 我的商品列表
 */
function myGoodsFunc() {
    $('.shelve').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
        downText: '下架',
        upText: '上架'
    });
    $('.delete-goods').button({
        loadingText: '<i class="fa fa-spinner fa-pulse"></i>',
    });

    /**
     * 商品上下架
     */
    $(document).on('click', '.shelve', function () {
        var self = $(this),
            id = self.data('id'),
            status = self.html() == '下架',
            statusInfo = self.closest('td').prev();
        // 判断登录
        if (!site.isLogin()) {
            site.redirect('auth/login');
            return;
        }

        self.button('loading');
        $.ajax({
            url: site.api('my-goods/shelve'),
            method: 'put',
            data: {status: !status ? 1 : 0, id: id}
        }).done(function (data, textStatus, jqXHR) {
            if ($.isPlainObject(data)) {
                status = data.message;
            } else {
                status = null;
            }
            if (status) {
                self.data('status', status).button('down');
                statusInfo.html('已上架');
            }else{
                self.data('status', status).button('up');
                statusInfo.html('已下架');
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

    $(document).on('click', '.delete-goods', function () {
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
            self.parents('tr').slideUp(function () {
                self.remove();
            })
        }).fail(function (jqXHR, textStatus, errorThrown) {
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
function statisticsFunc() {
    var target0 = $('input[name="order_page_num"]');
    var target_value0 = parseInt(target0.attr('value'));
    var target1 = $('input[name="goods_page_num"]');
    var target_value1 = parseInt(target1.attr('value'));
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
        var form = $('form');
        var oldUrl = form.attr('action');
        var newUrl = SITE.ROOT + "/order/stat-export";
        form.attr('action', newUrl).attr('method', 'post').submit();
        //初始化设置
        form.attr('action', oldUrl).attr('method', 'get');

    });
    $('#submitBtn').click(function () {
        target0.attr('value', 1);
        target1.attr('value', 1);
    });
    //order_page_num
    $('.next0').on('click', function () {
        target0.attr('value', target_value0 + 1);
    });
    $('.prev0').on('click', function () {
        target0.attr('value', target_value0 - 1);
    });
    //goods_page_num
    $('.next1').on('click', function () {
        target1.attr('value', target_value1 + 1);
    });
    $('.prev1').on('click', function () {
        target1.attr('value', target_value1 - 1);
    });
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

//function addGoodsFunc(cate1, cate2, cate3) {
//    var checkedLimit = 5, goodsImgsWrap = $('.goods-imgs');
//
//    function loadImg(cate1, cate2, cate3, url) {
//        var cate1 = cate1 || $('select[name="cate_level_1"]').val();
//        var cate2 = cate2 || $('select[name="cate_level_2"]').val();
//        var cate3 = cate3 || $('select[name="cate_level_3"]').val();
//        var url = url || site.api('my-goods/images');
//
//        var attrs = $('.attrs');
//        var array = new Array();
//        attrs.each(function () {
//            var val = $(this).val();
//            if (val > 0)
//                array.push(val);
//        });
//        var data = {
//            'cate_level_1': cate1,
//            'cate_level_2': cate2,
//            'cate_level_3': cate2 ? cate3 : '',
//            'attrs': array,
//        };
//        $.get(url, data, function (data) {
//
//            var html = '', goodsImage = data['goodsImage'], goodsImageData = goodsImage['data'], imageBox = $('.load-img-wrap');
//            for (var index in goodsImageData) {
//                html += '<div class="thumbnail col-xs-3 img-' + goodsImageData[index]['id'] + '">';
//                html += '   <img alt="" src="' + goodsImageData[index]['image_url'] + '" data-id="' + goodsImageData[index]['id'] + '">';
//                html += '</div>';
//            }
//            if (goodsImage['prev_page_url'] || goodsImage['next_page_url']) {
//                html += '<ul class="pager col-xs-12">';
//                if (goodsImage['prev_page_url'])
//                    html += '     <li><a class="prev0 page" href="javascript:void(0)" data-url="' + goodsImage['prev_page_url'] + '">上一页</a></li>'
//                if (goodsImage['next_page_url'])
//                    html += '     <li><a class="next0 page"  href="javascript:void(0)" data-url="' + goodsImage['next_page_url'] + '">下一页</a></li>'
//                html += '</ul>';
//            }
//
//            imageBox.html(html);
//        }, 'json');
//    }
//
//    $('.load-img-wrap').on('click', '.thumbnail', function () {
//
//        if (goodsImgsWrap.children('.thumbnail').length >= checkedLimit) {
//            return false;
//        }
//        var obj = $(this).children('img'), imgSrc = obj.attr('src'), imgId = obj.data('id');
//        var str = '<div class="thumbnail col-xs-3">';
//        str += '<button aria-label="Close" class="close" type="button">';
//        str += '    <span aria-hidden="true">×</span>';
//        str += '</button>';
//        str += '<img alt="" src="' + imgSrc + '">';
//        str += '<input type="hidden" value="' + imgId + '" name="images[]">';
//        str += '</div>';
//        goodsImgsWrap.append(str);
//        $(this).addClass('hidden');
//    });
//    goodsImgsWrap.on('click', '.close', function () {
//        var parents = $(this).closest('.thumbnail'), imageId = parents.find('input:hidden').val();
//        parents.fadeOut(500, function () {
//            $(this).remove();
//            $('.img-' + imageId).removeClass('hidden');
//        })
//    });
//    $('select.categories').change(function () {
//        $('.attr').html('');
//        loadImg()
//    });
//    $('.attr').on('change', '.attrs', function () {
//        loadImg()
//    });
//    $('.load-img-wrap').on('click', '.page', function () {
//        loadImg(null, null, null, $(this).data('url'));
//    });
//    //促销
//    $('input[name="is_promotion"]').change(function () {
//        var promotionInfo = $('textarea[name="promotion_info"]');
//        $(this).val() == 1 ? promotionInfo.prop('disabled', false) : promotionInfo.prop('disabled', true);
//    });
//    loadImg(cate1, cate2, cate3);
//}

function goodsBatchUpload() {
    $('#upload_file').change(function () {
        var fileName = $(this).val();
        var arr = fileName.split('\\');
        fileName = arr[arr.length - 1];
        $(this).closest('span').next('span').remove().end().after('<span>&nbsp;&nbsp;&nbsp;' + fileName + '</span>');
    });
    $('#upload_file').fileupload({
        dataType: 'json',
        add: function (e, data) {
            $(".save-btn").off('click').on('click', function () {
                var obj = $('#upload_file');
                obj.fileupload('disable');
                var $this = $(this),
                    cateLevel1 = $('select[name="cate_level_1"]').val(),
                    cateLevel2 = $('select[name="cate_level_2"]').val(),
                    cateLevel3 = $('select[name="cate_level_3"]').val() || 0,
                    status = /*$('input[name="status"]').is(':checked') ? 1 :*/ 0;
                if (!cateLevel1 || !cateLevel2) {
                    alert('请把分类选择完整');
                    return false;
                }
                obj.parent().addClass('disabled').siblings('.progress').show();
                obj.parent().siblings('.fileinput-error').remove();
                $(this).children('a').html('<i class="fa fa-spinner fa-pulse"></i> 操作中...');
                var formData = {
                    'status': status,
                    'cate_level_1': cateLevel1,
                    'cate_level_2': cateLevel2,
                    'cate_level_3': cateLevel3,
                };


                $('.attrs').each(function () {
                    var obj = $(this);
                    if (obj.val()) {
                        formData[obj.attr('name')] = obj.val();
                    }
                });
                data.formData = formData;
                data.submit();
            });
        }, fail: function (e, data) {
            var json = data.jqXHR['responseJSON'], text = '文件上传失败';
            if (json && json['message']) {
                text = json['message'];
            }
            $(this).parent().after('<span class="fileinput-error"> ' + text + '</span>');
            alert(text);
        },
        done: function (e, data) {
            $(this).parent().after('<span class="fileinput-error"> 上传成功</span>');
            alert('上传成功');
            location.reload();
        }, always: function (e, data) {
            // 隐藏进度条并开放按钮
            $(this).parent().removeClass('disabled').siblings('.progress').hide()
                .children('.progress-bar').css('width', '0');
            $(this).fileupload('enable');
            $(".save-btn a").html('保存');
        },
        progressall: function (e, data) {
            var progress = Math.round(data.loaded / data.total * 1000) / 10,
                text = isNaN(progress) ? '100.0%' : (progress.toFixed(1) + '%');
            $(this).parent().siblings('.progress')
                .children('.progress-bar').css('width', text).html(text);
        }
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
    $('.change-price').click(function () {
        var obj = $(this),
            order_id = obj.data('id'),
            pivot_id = obj.data('pivot'),
            price = obj.data('price'),
            num = obj.data('num');
        $('input[name="order_id"]').val(order_id);
        $('input[name="pivot_id"]').val(pivot_id);
        $('input[name="price"]').val(price);
        $('input[name="num"]').val(num);
    });
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
