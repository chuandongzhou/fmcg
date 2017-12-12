/**
 * Created by Colin on 2017/5/31.
 */
var Common = function () {
    this.loadingCount = 0;
    this.loadingTarget = undefined;
    this.data = [];
};

/**
 * 通用函数
 *
 * @type {{loading: Function}}
 */
Common.prototype = {
    loading: function (state) {
        switch (state) {
            case 'show':
                this.loadingCount++;
                break;
            case 'hide':
            case 'success':
                this.loadingCount--;
                break;
            case 'hideAll':
                this.loadingCount = 0;
                break;
        }

        var target = this.loadingTarget;

        // 显示 loading
        if (this.loadingCount > 0) {
            // 如果没有生成target，先生成target
            if (!target) {
                target = this.loadingTarget = $(
                    '<div class="mask"></div>'
                ).appendTo('body');
            }
            return target.show();
        }

        // 隐藏loading
        if (target) {
            if (state == 'success') {

            } else {
                target.hide();
            }
        }

        return target;
    }
};
var common = new Common();


// 通用异步表单提交
$(document.body)
// 按钮提交
    .on('click', '.mobile-ajax, form.mobile-ajax-form [type="submit"]', function () {
        var self = $(this)
            , form = self.hasClass('no-form') ? $([]) : self.closest('form');

        self.button({
            loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
            doneText: '操作成功',
            failText: '操作失败'
        });

        if (typeof tinymce === 'object') {
            tinyMCE.triggerSave();
        }
        var method = self.data('method') || form.attr('method')
            , url = self.data('url') || form.attr('action')
            , data = form.serializeArray()
            , delay = self.data('delay') || form.data('delay') || 1000
            , doneThen = self.data('doneThen') || form.data('doneThen')
            , doneUrl = self.data('doneUrl') || form.data('doneUrl')
            , danger = self.data('danger') || form.data('danger')
            , noPrompt = self.data('no-prompt') || form.data('no-prompt')
            , preventDefault = self.data('preventDefault') || form.data('preventDefault')
            , noLoading = self.data('no-loading') || form.data('no-loading');

        if (danger && !confirm(danger)) {
            self.prop('disabled', false);
            return false;
        }
        if (!noLoading) {
            common.loading('show');
        }
        clearTimeout(self.data('alwaysIntervalId'));
        form.formValidate('reset');

        // 序列化表单
        $.each(self.data('data') || {}, function (name, value) {
            data.push({name: name, value: value});
        });
        $.ajax({
            url: url,
            method: method,
            data: data
        }).done(function (data, textStatus, jqXHR) {
            common.loading('hide');
            var params = [data, textStatus, jqXHR, self];
            if (false !== self.triggerHandler('done.hct.ajax', params)
                && false !== form.triggerHandler('done.hct.ajax', params) && !preventDefault) {
                noPrompt || showMassage(self.data('doneText') || data.message || '操作成功');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            common.loading('hide');
            var params = [jqXHR, textStatus, errorThrown, self];
            if (false !== self.triggerHandler('fail.hct.ajax', params)
                && false !== form.triggerHandler('fail.hct.ajax', params)) {

                var json = jqXHR['responseJSON'];
                if (json) {
                    if (json['id'] == 'invalid_params') {
                        showMassage(apiv1FirstError(json))
                    } else {
                        showMassage(json['message']);
                    }
                }
            }

        }).always(function (data, textStatus, jqXHR) {
            if (textStatus == 'success' && data && !$.isPlainObject(data)) {
                var debugPanel = $('#ajaxDebugPanel');
                if (!debugPanel.length) {
                    debugPanel = $('<div id="ajaxDebugPanel" class="container"></div>').appendTo('body');
                }

                debugPanel.prepend('<div>' + data + '</div>');
                return;
            }

            var params = [data, textStatus, jqXHR, self];
            if (false !== self.triggerHandler('always.hct.ajax', params)
                && false !== form.triggerHandler('always.hct.ajax', params) && !preventDefault) {
                self.data('alwaysIntervalId', setTimeout(function () {
                    // 处理刷新事件
                    if (textStatus == 'success') {
                        if (doneUrl) {
                            window.location.href = doneUrl;
                        } else if (doneThen === undefined || doneThen == 'refresh') {
                            site.refresh(true);
                        } else if (doneThen == 'referer') {
                            site.redirectReferer();
                        } else {
                            self.button('reset');
                        }
                        return;
                    }

                    self.button('reset');
                }, delay));
            }
        });

        return false;
    })
    // 表单提交
    .on('submit', 'form.mobile-ajax-form', function (e) {
        e.preventDefault();
        return false;
    });

/**
 * 弹出提
 * @param content
 * @param delay
 */
var showMassage = function (content, delay) {
    delay = delay || 3;
    $('.popover-error-tips').find('.error-msg').html(content);
    if (typeof layer === 'object') {
        layer.open({
            content: $(".popover-error-tips").html(),
            className: 'popover-error',
            shade: false,
            time: delay
        });
    }
};

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
            minNum = num.data('minNum'),
            maxNum = num.data('maxNum');

        if (descNum.length && num.length && incNum.length) {
            descNum.on('click', '', function () {
                num.val(parseInt(num.val()) - 1);
                changeDescButton();
            });
            incNum.on('click', '', function () {
                var buyNum = parseInt(num.val());
                if (buyNum < maxNum) {
                    num.val(parseInt(num.val()) + 1);
                    changeDescButton();
                }
            });

            num.on('keyup', '', function () {
                var obj = $(this), buyNum = parseInt(obj.val());
                if (buyNum > maxNum) {
                    obj.val(maxNum);
                } else if (buyNum < minNum) {
                    obj.val(minNum);
                } else {
                    changeDescButton();
                }
            });

            var changeDescButton = function () {
                descNum.prop('disabled', num.val() <= minNum);

            }
        }
    });
};

/**
 * 登录js
 */
var roleSelect = function () {
    //点击出弹出层
    $(".select-role").click(function () {
        layer.open({
            title: false,
            content: $(".popover-role").html(),
            style: ' width:95%; height: auto;  padding:0;',
            shade: 'background-color: rgba(0,0,0,.3)'
        });
        $(".popover-panel").parent().addClass("pd-clear");
    })

    //弹窗选择角色登录
    $("body").on("click", ".select-role-wrap li a", function () {
        var obj = $(this), index = $(this).parents().index();
        $(".select-role img").eq(index).addClass("active").siblings().removeClass("active");
        $('form').find('input[name="type"]').val(obj.data('type')).trigger('change');
        layer.closeAll();
    })

}

/**
 * 点击感兴趣处理函数
 * @param {string} module         模块
 * @returns {undefined}
 */
var likeFunc = function () {
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
                showMassage(apiv1FirstError(jqXHR['responseJSON']));
            }
        });
    });
}

/**
 * 格式化地址
 * @param list
 * @param pid
 * @returns {Array}
 */
var formatAddress = function (list, pid) {
    // 创建Tree
    var tree = [];
    pid = pid || 1;
    var refer = [];
    for (var i in list) {
        refer[i] = list[i];
    }
    for (var i in list) {
        var data = list[i];
        // 判断是否存在parent
        var parentId = data[1];
        if (pid == parentId) {
            data.id = i;
            data.name = data[0];
            tree.push(data);
        } else {
            if (refer[parentId]) {
                var parent = refer[parentId];
                parent['child'] = parent['child'] || [];
                data.id = i;
                data.name = data[0];
                parent['child'].push(data);
            }
        }
    }
    return tree;
}

/**
 * 初始化地址
 * @param json
 * @param trigger
 * @param target
 * @param text
 * @param force
 * @param callback
 */
var addressSelect = function (json, trigger, target, callback, force) {
    var addressSelect = new MobileSelectArea()
        , value = target.data('id') && target.data('id').toString()
        , addressData = json
        , level = target.data('level') || 3
        , text = [];
    if (value) {
        var addressArr = value.split(',');
        for (var j in addressArr) {
            for (var i in addressData) {
                if (addressArr[j] == addressData[i].id) {
                    text.push(addressData[i]['name']);
                    addressData = addressData[i]['child'];
                    break;
                }
            }
        }
        !force && target.html(text.join(''));
    }
    addressSelect.init({
        trigger: trigger,
        value: value,
        level: level,
        text: text,
        data: json,
        eventName: 'click',
        position: 'bottom',
        callback: function (scroller, text, value) {
            callback(scroller, text, value);
        }
    });
}

/**
 * 设置街道
 * @param districtId
 * @param addressStreet
 * @param streetInput
 * @param areaNameInput
 * @param xLngInput
 * @param yLatInput
 */
var setStreetArea = function (districtId, addressStreet, streetInput, areaNameInput, xLngInput, yLatInput) {
    if (!districtId) {
        return false;
    }
    $.post(site.api('address/street'), {pid: districtId}, function (data) {
        var street = [];
        for (var i in data) {
            street.push({id: i, name: data[i]});
        }
        addressSelect(street, '#txt_street', addressStreet, function (scroller, streetName, streetId) {
            var areaName = $('#address-area').html() + streetName;
            streetInput.val(streetId[0]);
            areaNameInput.val(areaName);
            if (xLngInput) {
                var myGeo = new BMap.Geocoder();
                myGeo.getPoint(areaName, function (point) {
                    xLngInput.val(point.lng);
                    yLatInput.val(point.lat);
                });
            }

            addressStreet.html(streetName);
        });
    }, 'json')
}

/**
 * 获取图片路径
 * @param file
 * @returns {*}
 */
var getObjectURL = function (file) {
    var url = null;
    if (window.createObjectURL != undefined) { // basic
        url = window.createObjectURL(file);
    } else if (window.URL != undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file);
    } else if (window.webkitURL != undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}

// 通用文件上传
var imageUpload = function () {
    $('.image-upload  [type="file"]').each(function (index, obj) {
        var $this = $(obj), parent = $this.parent();

        $this.fileupload({
            dataType: 'json',
            formData: $this.data('data'),
            submit: function (e, data) {
                $(this).fileupload('disable');
                parent.addClass('disabled');
                parent.siblings('.fileinput-error').remove();
            },
            done: function (e, data) {
                var result = data.result;

                var name = $this.data('name') || 'image';
                // 设置隐藏域
                parent.find('.image').remove().end().siblings('.uploader-hidden').remove();

                // 设置图片预览
                parent.prepend('<img src="' + result.url + '" class="image">');
                parent.after('<input type="hidden" class="uploader-hidden" name="' + name + '" value="' + result.path + '">');

            },
            fail: function (e, data) {
                var json = data.jqXHR['responseJSON'], text = '文件上传失败';
                if (json && json['message']) {
                    text = json['message'];
                }

                parent.after('<span class="fileinput-error">' + text + '</span>');
            },
            always: function (e, data) {
                // 隐藏进度条并开放按钮
                parent.removeClass('disabled');
                $(this).fileupload('enable');
            }
        });
    });
}

/**
 * 地址选择
 * @type {Array}
 */
var addressChanged = function(addressData){
    var json = formatAddress(addressData)
        , addressArea = $('#address-area')
        , addressStreet = $('#address-street')
        , addressName = ''
        , provinceInput = $('input[name="address[province_id]"]')
        , cityInput = $('input[name="address[city_id]"]')
        , districtInput = $('input[name="address[district_id]"]')
        , streetInput = $('input[name="address[street_id]"]')
        , areaNameInput = $('input[name="address[area_name]"]');
    addressSelect(json, '#txt_area', addressArea, function (scroller, text, value) {
        addressStreet.html('');
        $('#txt_street').unbind('click');
        addressName = text.join('');
        addressArea.html(addressName);
        provinceInput.val(value[0]);
        cityInput.val(value[1]);
        districtInput.val(value[2]);
        streetInput.val(0);
        areaNameInput.val(addressName);
        if (value[2]) {
            setStreetArea(value[2], addressStreet, streetInput, areaNameInput);
        }
    });
}

