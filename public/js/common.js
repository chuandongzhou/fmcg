function Site(object) {
    this.userInfo = object.USER;
    this.baseUrl = object.ROOT;
    this.apiUrl = object.API_ROOT;
}

Site.prototype = {
    /**
     * 获取网站url
     *
     * @param {string} [path]
     * @returns {string}
     */
    url: function (path) {
        return this.baseUrl + (path ? '/' + path : '');
    },

    /**
     * 获取接口url
     *
     * @param {string} [path]
     * @returns {string}
     */
    api: function (path) {
        return this.apiUrl + (path ? '/' + path : '');
    },

    /**
     * 是否已经登录
     *
     * @returns {boolean}
     */
    isLogin: function () {
        return !$.isEmptyObject(this.userInfo);
    },

    /**
     * 判断是否使用手机浏览器
     *
     * @returns {boolean}
     */
    inMobile: function () {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },

    /**
     * 获取登录用户信息
     *
     * @param {string} [key]
     * @returns {*}
     */
    user: function (key) {
        if (typeof key == 'undefined') {
            return this.userInfo;
        }

        return this.userInfo && this.userInfo[key];
    },

    /**
     * 刷新页面
     *
     * @param {boolean} [force]
     */
    refresh: function (force) {
        window.location.reload(force);
    },

    /**
     * 重定向到某个页面
     *
     * @param {string} [path]
     */
    redirect: function (path) {
        window.location.href = this.url(path);
    },

    /**
     * 保留当前页面的重定向
     *
     * @param {string} [path]
     */
    //redirectGuest: function (path) {
    //    Cookies.set('url_intended', window.location.href);
    //    this.redirect(path);
    //},

    /**
     * 尝试按照原路返回
     *
     * @param {string} [defaultPath]
     */
    //redirectIntended: function (defaultPath) {
    //    var intended = Cookies.get('url_intended');
    //    Cookies.remove('url_intended');
    //    window.location.href = intended ? intended : this.url(defaultPath);
    //},

    /**
     * 重定向到上一页面
     *
     * @param {string} [defaultPath]
     */
    redirectReferer: function (defaultPath) {
        var referer = window.document.referrer;

        if (!referer && typeof defaultPath === 'undefined') {
            this.refresh(true);
        } else {
            var url = referer ? referer : this.url(defaultPath);
            window.location.href = url;
        }
    }
};

/**
 * 实例化全局对象
 */
var site = new Site(SITE);

var Common = function () {

    this.loadingCount = 0;
    this.loadingTarget = undefined;

}, common = new Common();

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
                return this.loadingTarget = $(
                    '<div class="modal fade"> ' +
                    '    <div class="modal-dialog modal-sm"> ' +
                    '        <div class="modal-content"> ' +
                    '            <div class="modal-body text-center"> ' +
                    '                <i class="fa fa-spinner fa-pulse"></i> 加载中... ' +
                    '            </div>' +
                    '            <div class="modal-footer text-right collapse"> ' +
                    '                <button class="btn btn-primary btn-sm">确定</button>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>'
                ).modal({backdrop: 'static', keyboard: false});
            }

            return target.modal('show');
        }

        // 隐藏loading
        if (target) {
            if (state == 'success') {

            } else {
                target.modal('hide');
            }
        }

        return target;
    }
};

/**
 * 空函数
 */
var noop = function () {
};

/**
 * 获取api返回的第一个错误
 *
 * @param {object} json
 * @param {string} [defaultValue]
 */
function apiv1FirstError(json, defaultValue) {
    if ($.isPlainObject(json)) {
        var errors = json['errors'], message = json['message'];
        if ($.isPlainObject(errors)) {
            var firstValue = errors[Object.keys(errors)[0]];
            if ($.isArray(firstValue) && firstValue.length > 0) {
                return firstValue[0];
            } else if (firstValue) {
                return firstValue;
            }
        } else if (message) {
            return message;
        }
    }

    return defaultValue;
}

/**
 * js错误报告
 */
var jsErrorReportSetup = function () {
    var oldOnError = window.onerror;

    window.onerror = function (errorMsg, url, lineNumber, column, errorObj) {
        if (oldOnError) oldOnError.apply(this, arguments);

        var data = {
            error_msg: errorMsg,
            url: url,
            line_number: lineNumber,
            column: column,
            error_obj: errorObj
        };

        $.ajax({
            url: site.api('js-errors'),
            method: 'post',
            data: data
        });
        return false;
    };
};

/**
 * 通用全局函数调用
 *
 * @param {string} functionName
 * @returns {*}
 */
function executeFunctionByName(functionName /*, args */) {
    var args = [].slice.call(arguments).splice(2);
    var namespaces = functionName.split(".");
    var context = window;

    for (var i = 0; i < namespaces.length; i++) {
        if (!(context = context[namespaces[i]])) {
            return;
        }
    }

    return context.apply(this, args);
}

/**
 * 通用jquery扩展方法
 */
var commonJQueryExtendSetup = function () {
    (function ($) {

        /**
         * 表单验证结果
         * @param object validates
         * @param [string] state
         * @returns {$.fn}
         */
        $.fn.formValidate = function (validates, state) {
            var self = this;

            // 清空之前的表单错误信息
            this.find('[class*="has-"]').removeClass(function (index, css) {
                return (css.match(/(^|\s)has-\S+/g) || []).join(' ');
            }).find('.ajax-error').remove();

            if (validates == 'reset' || !$.isPlainObject(validates)) {
                return this;
            }

            state = state || 'error';
            $.each(validates, function (name, messages) {
                var formGroup = self.find('[name="' + name + '"]').closest('.form-group').addClass('has-' + state)
                    , helpBlock = formGroup.find('.ajax-error');

                if (!helpBlock.length) {
                    helpBlock = $('<p class="help-block ajax-error"></p>').addClass(self.data('help-class')).appendTo(formGroup);
                }

                $.each(messages, function (index, message) {
                    var html = helpBlock.html();
                    if (html && html.length > 0) {
                        message = '， ' + message;
                    }

                    helpBlock.append(message);
                });
            });

            return this;
        }

    }(jQuery));
};

/**
 * 通用ajax设置
 */
var commonAjaxSetup = function () {
    // 设置全局 xsrf-token
    jQuery.ajaxSetup({
        headers: {
            'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
        }
    });

    // 通用异步表单提交
    $(document.body)
        // 按钮提交
        .on('click', '.ajax, form.ajax-form [type="submit"]', function () {
            var self = $(this)
                , form = self.hasClass('no-form') ? $([]) : self.closest('form')
                , isButton = self.hasClass('btn');

            if (isButton) {
                self.button({
                    loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
                    doneText: '操作成功',
                    failText: '操作失败'
                });
            }

            if (typeof tinymce == 'object') {
                tinyMCE.triggerSave();
            }
            var method = self.data('method') || form.attr('method')
                , url = self.data('url') || form.attr('action')
                , data = form.serializeArray()
                , delay = self.data('delay') || form.data('delay') || 3000
                , preventDefault = self.data('preventDefault') || form.data('preventDefault')
                , doneThen = self.data('doneThen') || form.data('doneThen')
                , doneUrl = self.data('doneUrl') || form.data('doneUrl');

            isButton && self.button('loading') && clearTimeout(self.data('alwaysIntervalId'));
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
                var params = [data, textStatus, jqXHR, self];
                if (false !== self.triggerHandler('done.hct.ajax', params)
                    && false !== form.triggerHandler('done.hct.ajax', params)
                    && !preventDefault) {
                    isButton && self.button('done');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                var params = [jqXHR, textStatus, errorThrown, self];
                if (false !== self.triggerHandler('fail.hct.ajax', params)
                    && false !== form.triggerHandler('fail.hct.ajax', params)
                    && !preventDefault) {
                    isButton && self.button('fail');

                    var json = jqXHR['responseJSON'];
                    if (json) {
                        console.log(json);
                        if (json['id'] == 'invalid_params') {
                            form.formValidate(json['errors']);
                        } else {
                            isButton && setTimeout(function () {
                                self.html(json['message']);
                            }, 0);
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
                    && false !== form.triggerHandler('always.hct.ajax', params)
                    && !preventDefault) {
                    isButton && self.data('alwaysIntervalId', setTimeout(function () {
                        // 处理刷新事件
                        if (textStatus == 'success') {
                            if (doneUrl) {
                                window.location.href = doneUrl;
                            } else if (doneThen === undefined || doneThen == 'refresh') {
                                site.refresh(true);
                            } else if (doneThen == 'referer') {
                                site.redirectReferer();
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
        .on('submit', 'form.ajax-form', function (e) {
            e.preventDefault();
            return false;
        });
};

/**
 * 通用上传设置
 */
var commonUploadSetup = function () {
    if (!$.support.fileInput) {
        return;
    }

    // 通用文件上传
    $('.fileinput-button > [type="file"]').each(function (index, obj) {
        var $this = $(obj), parent = $this.parent();

        $this.fileupload({
            dataType: 'json',
            formData: $this.data('data'),
            submit: function (e, data) {
                parent.addClass('disabled').prop('disabled', true).siblings('.progress').show();
                parent.siblings('.fileinput-error').remove();
            },
            done: function (e, data) {
                var result = data.result, name = parent.data('name') || 'image';
                // 设置图片预览
                parent.siblings('.image-preview').children('img').attr('src', result.url);
                // 设置隐藏域
                parent.siblings('.uploader-hidden').remove();
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
                parent.removeClass('disabled').siblings('.progress').hide()
                    .children('.progress-bar').css('width', '0');
            },
            progressall: function (e, data) {
                var progress = Math.round(data.loaded / data.total * 1000) / 10,
                    text = isNaN(progress) ? '100.0%' : (progress.toFixed(1) + '%');
                parent.siblings('.progress')
                    .children('.progress-bar').css('width', text).html(text);
            }
        });
    });
};

/**
 * 添加地址
 */
var addAddFunc = function () {
    var container = $('.address-list')
        , addButton = $('#add-address')
        , btnAdd = $('.btn-add')
        , addLimit = 5, province = $('.add-province')
        , city = $('.add-city')
        , district = $('.add-district')
        , street = $('.add-street')
        , address = $('.address');

    $('#addressModal').on('hidden.bs.modal', function (e) {
        //TODO 初始化地址
        address.val('');
    });
    // 地址限制
    var changeAddButtonStatus = function () {
        if (container.children('div:visible').length >= addLimit) {
            addButton.button('loading');
            return true;
        }

        addButton.button('reset');
        return false;
    };
    var changeBtnAddhtml = function (html) {
        btnAdd.html(html);
        setTimeout(function () {
            btnAdd.html(btnAdd.data('text'));
        }, 3000)
    };

    changeAddButtonStatus();
    // 删除地址
    container.on('click', '.close', function () {
        $(this).parent().fadeOut('normal', function () {
            $(this).remove();

            changeAddButtonStatus();
        });
    });

    // 添加地址
    btnAdd.on('click', '', function () {
        if (!province.val()) {
            changeBtnAddhtml('请选择省市...');
            return false;
        }
        if (!city.val()) {
            changeBtnAddhtml('请选择城市...');
            return false;
        }
        if (district.is(':visible') && !district.val()) {
            changeBtnAddhtml('请选择区县...');
            return false;
        }

        if (street.is(':visible') && !street.val()) {
            changeBtnAddhtml('请选择街道...');
            return false;
        }
        if (!address.val()) {
            changeBtnAddhtml('请输入详细地址');
            return false;
        }
        var provinceText = province.find("option:selected").text(),
            cityText = city.find("option:selected").text(),
            districtText =district.is(':visible') ? district.find("option:selected").text() : '',
            streetText = street.is(':visible') ? street.find("option:selected").text() : '',
            addressText = address.val(),
            areaName = provinceText + ' ' + cityText + ' ' + districtText + ' ' + streetText
        $('.btn-close').trigger('click');
        container.prepend(
            '<div class="col-sm-12 fa-border">' +
            areaName+
            addressText +
            '<input type="hidden" name="area[id][]" value=""/>' +
            '<input type="hidden" name="area[province_id][]" value="' + province.val() + '"/>' +
            '<input type="hidden" name="area[city_id][]" value="' + city.val() + '"/>' +
            '<input type="hidden" name="area[district_id][]" value="' + district.val() + '"/>' +
            '<input type="hidden" name="area[street_id][]" value="' + street.val() + '"/>' +
            '<span class="fa fa-times-circle pull-right close"></span>' +
            '<input type="hidden" name="area[area_name][]" value="' + areaName + '"/>'+
            '<input type="hidden" name="area[address][]" value="' + addressText + '"/>',
            '</div>'
        );
        changeAddButtonStatus();
    });
}

var getCategory = function (url) {
    var level1 = $('select[name="cate_level_1"]')
        , level2 = $('select[name="cate_level_2"]')
        , level3 = $('select[name="cate_level_3"]');


    var post = function (pid, select) {
        var ohtml = '<option value="">请选择</option>';
        $.get(url, {pid: pid}, function (data) {
            for (var index in data['message']) {
                ohtml += '<option value="' + index + '">' + data['message'][index] + '</option>'
            }
            select.html(ohtml);
        }, 'json')
    };
    level1.change(function () {
        post($(this).val(), level2);
        level3.html('<option value="">请选择</option>');
    });
    level2.change(function () {
        post($(this).val(), level3);
    })
}

/**
 * 获取所有的分类
 * @param url
 * @param level1
 * @param level2
 * @param level3
 */
var getAllCategory = function (url, level1, level2, level3) {
    level2 = level2 || 0;
    level3 = level3 || 0;
    var params = {
        'level1': level1,
        'level2': level2,
        'level3': level3,
    };
    $.get(url, params, function (data) {
        var level1Info = data.level1;
        var level2Info = data.level2;
        var level3Info = data.level3;
        addOption(level1Info, $('select[name="cate_level_1"]'), level1);
        addOption(level2Info, $('select[name="cate_level_2"]'), level2, '<option value="">请选择</option>');
        addOption(level3Info, $('select[name="cate_level_3"]'), level3, '<option value="">请选择</option>');
    }, 'json');

    var addOption = function (data, selectObj, selectedId, prefix) {
        var htmls = prefix || '';
        for (var key in data) {
            if (selectedId == key) {
                htmls += "<option value='" + key + "' selected>" + data[key] + "</option>";
            } else {
                htmls += "<option value='" + key + "'>" + data[key] + "</option>";
            }
        }
        selectObj.html(htmls);
    }
}


/**
 * 通用裁剪Modal
 */
var commonCropSetup = function () {
    $('[data-target="#cropperModal"]').on('cropped.hct.cropper', '', function (e, data) {
        var self = $(this), name = self.data('name') || 'image';

        self.siblings('.image-preview').children('img').attr('src', data.url);
        self.siblings('.cropper-hidden').remove();
        self.before('<input type="hidden" class="cropper-hidden" name="' + name + '" value="' + data.path + '">');
    });
};


/**
 * 通用方法设置
 */
var commonMethodSetup = function () {
    // 全局placeholder
    $('input, textarea').placeholder();

    // 全局限制textarea长度
    $.valHooks.textarea = {
        get: function (elem) {
            return elem.value.replace(/\r?\n/g, "\r\n");
        }
    };

    $("textarea[maxlength]").each(function (index, obj) {
        var $this = $(obj), counter = $this.siblings('.textarea-counter'), maxLength = $this.attr('maxlength');

        $this.on('input propertychange', function () {
            var $this = $(this), val = $this.val(), length = val.length;
            if (val.length > maxLength) {
                $this.val(val.substring(0, maxLength));
                length = maxLength;
            }
            counter.html(length + ' / ' + maxLength);
        });

        counter.html($this.val().length + ' / ' + maxLength);
    });

    // 通用表格全选 反选
    $(document.body)
        // 全选
        .on('click', '.common-select-all', function () {
            $(this).closest('tr').siblings('tr').find('td:first-child input[type="checkbox"]').prop('checked', true);
        })
        // 反选
        .on('click', '.common-select-flip', function () {
            $(this).closest('tr').siblings('tr').find('td:first-child input[type="checkbox"]').each(function () {
                var self = $(this);
                self.prop('checked', !self.prop('checked'));
            });
        })
        // 取消全选
        .on('click', '.common-select-clear', function () {
            $(this).closest('tr').siblings('tr').find('td:first-child input[type="checkbox"]').prop('checked', false);
        });

    // Bootstrap默认设置
    $.fn.button.Constructor.DEFAULTS.loadingText = '请稍后...';
};

/**
 * 添加图片
 */
function picFunc() {
    var container = $('.pictures')
        , uploadButton = $('#pic-upload')
        , uploadLimit = 5;

    // 图片上传限制
    var changeUploadButtonStatus = function () {
        if (container.children('div:visible').length >= uploadLimit) {
            uploadButton.button('loading');
            return true;
        }

        uploadButton.button('reset');
        return false;
    };
    changeUploadButtonStatus();

    // 删除图片
    container.on('click', '.close', function () {
        $(this).closest('.thumbnail').parent().fadeOut('normal', function () {
            $(this).remove();

            changeUploadButtonStatus();
        });
    });

    // 裁剪事件通知
    $('#pic-upload').on('cropped.hct.cropper', '', function (e, data) {
        e.stopImmediatePropagation();

        var name = data.org_name;
        name = name.substr(0, name.lastIndexOf('.')) || name;
        container.prepend('' +
            '<div class="col-xs-3"> ' +
            '   <div class="thumbnail"> ' +
            '       <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button> ' +
            '       <img src="' + data.url + '" alt=""> ' +
            '       <input type="hidden" name="images[id][]" value=""> ' +
            '       <input type="hidden" name="images[path][]" value="' + data.path + '"> ' +
            '       <input type="hidden" name="images[org_name][]" value="' + data.org_name + '"> ' +
            '       <input type="text" class="form-control input-sm" name="images[name][]" value="' + name + '"> ' +
            '   </div>' +
            '</div>');

        changeUploadButtonStatus();
    });
}


/**
 * 初始化方法
 */
jQuery(function () {
    jsErrorReportSetup();

    // 通用jQuery扩展设置
    commonJQueryExtendSetup();

    // 通用ajax设置
    commonAjaxSetup();

    // 通用上传方法
    commonUploadSetup();

    // 通用裁剪方法
    commonCropSetup();

    // 通用杂项方法设置
    commonMethodSetup();
});

if (!window.console) {
    var console = {
        log: noop,
        warn: noop,
        error: noop,
        time: noop,
        timeEnd: noop
    }
}