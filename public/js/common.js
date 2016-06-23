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
 * 设置cookie
 * @param name
 * @param value
 */
function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + value + ";expires=" + exp.toGMTString() + ";path=/";
}

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
            var self = this, firstControl = true;

            // 清空之前的表单错误信息
            this.find('[class*="has-"]').removeClass(function (index, css) {
                return (css.match(/(^|\s)has-\S+/g) || []).join(' ');
            }).find('.ajax-error').remove();

            if (validates == 'reset' || !$.isPlainObject(validates)) {
                return this;
            }

            state = state || 'error';

            $.each(validates, function (name, messages) {
                var control = self.find('[name="' + name + '"]');
                if (firstControl) {
                    control.focus();
                    firstControl = false;
                }
                var formGroup = control.closest('.form-group').addClass('has-' + state)
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
                    isButton && self.html(data.message || '操作成功');
                    self.hasClass('no-prompt') || alert(self.data('doneText') || data.message || '操作成功');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                var params = [jqXHR, textStatus, errorThrown, self];
                if (false !== self.triggerHandler('fail.hct.ajax', params)
                    && false !== form.triggerHandler('fail.hct.ajax', params)
                    && !preventDefault) {
                    isButton && self.html('操作失败');

                    var json = jqXHR['responseJSON'];
                    if (json) {
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
        var $this = $(obj), parent = $this.parent(), multi = $this.data('multi'), container = $('.pictures');

        $this.fileupload({
            dataType: 'json',
            formData: $this.data('data'),
            submit: function (e, data) {
                $(this).fileupload('disable');
                parent.addClass('disabled').siblings('.progress').show();
                parent.siblings('.fileinput-error').remove();
            },
            done: function (e, data) {
                var result = data.result;
                if (multi) {
                    var name = result.org_name;
                    name = name.substr(0, name.lastIndexOf('.')) || name;
                    container.prepend('' +
                        '<div class="col-xs-3"> ' +
                        '   <div class="thumbnail"> ' +
                        '       <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button> ' +
                        '       <img src="' + result.url + '" alt=""> ' +
                        '       <input type="hidden" name="images[id][]" value=""> ' +
                        '       <input type="hidden" name="images[path][]" value="' + result.path + '"> ' +
                        '       <input type="hidden" name="images[org_name][]" value="' + result.org_name + '"> ' +
                        '       <input type="text" class="form-control input-sm" name="images[name][]" value="' + name + '"> ' +
                        '   </div>' +
                        '</div>');
                } else {
                    var name = parent.data('name') || 'image';
                    // 设置图片预览
                    parent.siblings('.image-preview').children('img').attr('src', result.url);
                    // 设置隐藏域
                    parent.siblings('.uploader-hidden').remove();
                    parent.after('<input type="hidden" class="uploader-hidden" name="' + name + '" value="' + result.path + '">');
                }
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
                $(this).fileupload('enable');
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
    //, addButton = $('#add-address')
        , btnAdd = $('.btn-add')
    //, addLimit = 500   //最大地址限制
        , province = $('.add-province')
        , city = $('.add-city')
        , district = $('.add-district')
        , street = $('.add-street')
        , address = $('.detail-address');

    //$('#addressModal').on('hidden.bs.modal', function (e) {
    //    //TODO 初始化地址
    //    address.val('');
    //});
    // 地址限制
    /* var changeAddButtonStatus = function () {
     if (container.children('div:visible').length >= addLimit) {
     addButton.button('loading');
     return true;
     }

     addButton.button('reset');
     return false;
     };*/
    var changeBtnAddhtml = function (html) {
        btnAdd.html(html);
        setTimeout(function () {
            btnAdd.html(btnAdd.data('text'));
        }, 3000)
    };

    //changeAddButtonStatus();
    // 删除地址
    container.on('click', '.close', function () {
        $(this).parent().fadeOut('normal', function () {
            $(this).remove();
            //changeAddButtonStatus();
            //dynamicShowMap();
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

        /* if (street.is(':visible') && !street.val()) {
         changeBtnAddhtml('请选择街道...');
         return false;
         }
         if (!address.val()) {
         changeBtnAddhtml('请输入详细地址');
         return false;
         }*/
        var provinceText = province.find("option:selected").text(),
            cityText = city.find("option:selected").text(),
            districtText = district.is(':visible') ? district.find("option:selected").text() : '',
            streetText = !street.is(':visible') || street.find("option:selected").text() == '请选择街道...' ? '' : street.find("option:selected").text(),
            addressText = address.val(),
            areaName = provinceText + cityText + districtText + streetText;
        $('.modal-header .close').trigger('click');
        container.prepend(
            '<div class="col-sm-10 fa-border show-map">' +
            areaName + addressText +
            '<input type="hidden" name="area[id][]" value=""/>' +
            '<input type="hidden" name="area[province_id][]" value="' + province.val() + '"/>' +
            '<input type="hidden" name="area[city_id][]" value="' + city.val() + '"/>' +
            '<input type="hidden" name="area[district_id][]" value="' + district.val() + '"/>' +
            '<input type="hidden" name="area[street_id][]" value="' + street.val() + '"/>' +
            '<span class="fa fa-times-circle pull-right close"></span>' +
            '<input type="hidden" name="area[area_name][]" value="' + areaName + '"/>' +
            '<input type="hidden" name="area[address][]" value="' + addressText + '"/>' +
            //'<input type="hidden" name="area[blx][]" value="' + $('input[name="coordinate_blx"]').val() + '"/>' +
            //'<input type="hidden" name="area[bly][]" value="' + $('input[name="coordinate_bly"]').val() + '"/>' +
            //'<input type="hidden" name="area[slx][]" value="' + $('input[name="coordinate_slx"]').val() + '"/>' +
            //'<input type="hidden" name="area[sly][]" value="' + $('input[name="coordinate_sly"]').val() + '"/>' +
            '</div>'
        );
        //changeAddButtonStatus();
        //dynamicShowMap();
    });
};

/**
 * 根据pid获取子分类
 * @param url
 */
var getCategory = function (url) {
    var level1 = $('select[name="cate_level_1"]')
        , level2 = $('select[name="cate_level_2"]')
        , level3 = $('select[name="cate_level_3"]');


    var post = function (pid, select) {
        var ohtml = '<option value="">请选择</option>';
        $.get(url, {pid: pid}, function (data) {
            for (var index in data) {
                ohtml += '<option value="' + index + '">' + data[index] + '</option>'
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
        addOption(level1Info, $('select[name="cate_level_1"]'), level1, '<option value="">请选择</option>');
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
var picFunc = function (uploadLimit) {
    var container = $('.pictures')
        , uploadButton = $('#pic-upload')
        , uploadLimit = uploadLimit || 5;

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
 * 获取标签
 */
var getAttr = function () {
    //获取标签
    $('select[name="cate_level_1"]').change(function () {
        $('div.attr').html('');
    });

    var attrDiv = $('.attr');
    $('select[name="cate_level_2"] , select[name="cate_level_3"]').change(function () {
        var categoryId = $(this).val() || $('select[name="cate_level_2"]').val();

        if (categoryId > 0) {
            $.get(site.api('categories/' + categoryId + '/attrs'), {
                category_id: categoryId,
                format: true
            }, function (data) {
                var html = '';
                for (var index in data) {
                    var options = '<option value="0">请选择</option>';
                    html += '<div class="items-item">';
                    html += '<label>' + data[index]['name'] + '</label>';
                    html += ' <select name="attrs[' + data[index]['attr_id'] + ']" class="attrs inline-control" >';
                    for (var i in data[index]['child']) {
                        options += ' <option value="' + data[index]['child'][i]['attr_id'] + '">' + data[index]['child'][i]['name'] + '</option>'
                    }
                    html += options;
                    html += '</select>';
                    html += '</div>';
                }
                attrDiv.html(html).css('border', '1px solid #b4b4b4');
            }, 'json')
        } else {
            attrDiv.html('').css('border', '1px solid #fff');
        }
    });
    attrDiv.children().length == 0 ? attrDiv.css('border', '1px solid #fff') : attrDiv.css('border', '1px solid #b4b4b4');
}

/**
 * address页面百度地图相关Js
 */
var baiDuMap = function () {
    //初始化这个变量,防止百度地图重复实例化所导致的显示错误问题
    var flag = false;
    if (!flag) {
        var map_modal = new BMap.Map("map-modal", {enableMapClick: false});
        map_modal.addControl(new BMap.NavigationControl());
        map_modal.addControl(new BMap.ScaleControl());
        map_modal.addControl(new BMap.OverviewMapControl());
        map_modal.addControl(new BMap.MapTypeControl());
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
            if (areaName != '其它区' && areaName != '海外') {
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

                            var northEast = coordinate.getNorthEast();
                            var southWest = coordinate.getSouthWest();
                            $('input[name="bl_lng"]').val(northEast.lng);
                            $('input[name="bl_lat"]').val(northEast.lat);
                            $('input[name="sl_lng"]').val(southWest.lng);
                            $('input[name="sl_lat"]').val(southWest.lat);
                            polygon_modal.addEventListener('lineupdate', function () {
                                coordinate = polygon_modal.getBounds();
                                northEast = coordinate.getNorthEast();
                                southWest = coordinate.getSouthWest();

                                $('input[name="bl_lng"]').val(northEast.lng);
                                $('input[name="bl_lat"]').val(northEast.lat);
                                $('input[name="sl_lng"]').val(southWest.lng);
                                $('input[name="sl_lat"]').val(southWest.lat);
                            });
                        }
                    }
                }, areaName);

            }
        }

    });
}

var dynamicShowMap = function () {
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
var getCoordinateMap = function (data) {
    var map = new BMap.Map("map", {enableMapClick: false});
    //左上角，添加默认缩放平移控件
    map.addControl(new BMap.NavigationControl());
    map.addControl(new BMap.ScaleControl());
    map.addControl(new BMap.OverviewMapControl());
    map.addControl(new BMap.MapTypeControl());

    if (data && data.length) {
        $.each(data, function (index, value) {
            if (value.coordinate) {
                var point_lng = parseFloat(value['coordinate']['sl_lng']) + (value['coordinate']['bl_lng'] - value['coordinate']['sl_lng']) / 2;
                var point_lat = parseFloat(value['coordinate']['sl_lat']) + (value['coordinate']['bl_lat'] - value['coordinate']['sl_lat']) / 2;
                var point = new BMap.Point(point_lng, point_lat);
                if (!index) {
                    //map.centerAndZoom(point,5);//取第一个中心点为地图默认中心
                    map.centerAndZoom(new BMap.Point(point_lng, point_lat), 13);
                    map.setZoom(12); //默认到市
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
            }
        });

    } else {
        map.centerAndZoom(new BMap.Point(106, 35), 5);

    }
};

var getShopAddressMap = function (lng, lat) {
    var addressMap = new BMap.Map('address-map', {enableMapClick: false});
    var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
    addressMap.addControl(top_left_navigation);
    addressMap.enableScrollWheelZoom(true)
    if (lng && lat) {
        var point_address = new BMap.Point(lng, lat);
        addressMap.centerAndZoom(point_address, 12);
        addressMap.addOverlay(new BMap.Marker(point_address));
    } else {
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
    $('.shop-address').on('change', 'select', function () {
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
        if (areaName != '其它区' && areaName != '海外') {
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
                    newMarker.addEventListener('dragend', function () {
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
 * 设置地址
 * @param setMap
 * @param baiduMap
 */
var addressSelectChange = function (setMap, baiduMap) {
    $('select.address').change(function () {
        var obj = $(this), addressPanel = obj.closest('.address-panel'),
            areaName = addressPanel.find('.area-name'),
            provinceControl = addressPanel.find('select.address-province'),
            cityControl = addressPanel.find('select.address-city'),
            districtControl = addressPanel.find('select.address-district'),
            streetControl = addressPanel.find('select.address-street'),
            provinceVal = provinceControl.val() ? provinceControl.find("option:selected").text() : '',
            cityVal = cityControl.val() ? cityControl.find("option:selected").text() : '',
            districtVal = districtControl.val() ? districtControl.find("option:selected").text() : '',
            streetVal = streetControl.val() ? streetControl.find("option:selected").text() : '';
        areaName.val(provinceVal + cityVal + districtVal + streetVal);
        if (setMap) {
            var group = obj.data('group') || 'default',
                cityName = obj.find('option:checked').text(),
                lngControl = addressPanel.find('.lng'),
                latControl = addressPanel.find('.lat');
            var num = 6;
            if (obj.hasClass('address-city')) {
                num = 12;
            }
            if (obj.hasClass('address-district')) {
                num = 14;
            }
            if (obj.hasClass('address-street')) {
                num = 16;
            }
            if (cityName != '其它区' && cityName != '海外') {
                //删除之前的覆盖物
                baiduMap[group].clearOverlays();
                // 创建地址解析器实例
                var myGeo = new BMap.Geocoder();
                // 将地址解析结果显示在地图上,并调整地图视野
                myGeo.getPoint(cityName, function (newPoint) {
                    if (newPoint) {
                        point_address = newPoint;
                        baiduMap[group].centerAndZoom(newPoint, num);
                        var newMarker = new BMap.Marker(point_address);
                        //重置中心点
                        baiduMap[group].addOverlay(newMarker);
                        var pointPosition = newMarker.getPosition();
                        lngControl.val(pointPosition.lng);
                        latControl.val(pointPosition.lat);
                        newMarker.enableDragging();//可拖拽点
                        newMarker.addEventListener('dragend', function () {
                            pointPosition = newMarker.getPosition();
                            lngControl.val(pointPosition.lng);
                            latControl.val(pointPosition.lat);
                        });
                    }
                }, cityName);
            }
        }
    });
};

/**
 * 初始化并返回地图
 * @returns {Array}
 */
var initMap = function () {
    var baiduMap = new Array();
    $('.baidu-map').each(function () {
        var obj = $(this),
            group = obj.data('group') || 'default',
            lng = parseFloat(obj.data('lng')),
            lat = parseFloat(obj.data('lat')),
            top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件

        console.log(lng);
        console.log(lat);

        baiduMap[group] = new BMap.Map(group, {enableMapClick: false});
        baiduMap[group].addControl(top_left_navigation);
        baiduMap[group].enableScrollWheelZoom(true);

        if (lng && lat) {
            var point_address = new BMap.Point(lng, lat);
            baiduMap[group].centerAndZoom(point_address, 12);
            baiduMap[group].addOverlay(new BMap.Marker(point_address));
        } else {
            var point_address = new BMap.Point(106, 35);
            baiduMap[group].centerAndZoom(point_address, 12);
            var geolocation_address = new BMap.Geolocation();
            geolocation_address.getCurrentPosition(function (r) {
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    //重置中心点
                    baiduMap[group].addOverlay(new BMap.Marker(r.point));
                    baiduMap[group].panTo(r.point);
                }
            }, {enableHighAccuracy: true});
        }

    });
    return baiduMap;
}


/**
 * get提交form处理
 * @param exceptName
 */
var formSubmitByGet = function (exceptName) {
    exceptName = exceptName || [];

    $('.search-by-get').on('click', function () {
        var obj = $(this), form = obj.closest('form'), query = new Array(), action = form.attr('action');


        $.each(form.serializeArray(), function (i, o) {
            if (o.value && $.inArray(o.name, exceptName) == -1) {
                query.push(o.name + '=' + o.value);
            }
        });

        var povit = action.indexOf('?') >= 0 ? '&' : '?', queryString = query.length ? povit + query.join('&') : '';
        window.location.href = action + queryString;
        return false;
    })
}

/**
 * 全选
 * @param parent_selectors
 * @param target_selectors
 */
var onCheckChange = function (parent_selectors, target_selectors) {
    var parents = $(parent_selectors);
    var targets = $(target_selectors);
    parents.change(function () {
        targets.prop('checked', $(this).prop('checked'));
    });

    targets.change(function () {
        parents.prop('checked', targets.length === targets.filter(':checked').length);
    });
}

/**
 * 商品批量上传处理
 */
var goodsBatchUpload = function () {
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
                    shopIdControl = $('input[name="shop_id"]'),
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
                if (shopIdControl.length && shopIdControl.val()) {
                    formData['shop_id'] = shopIdControl.val();
                }

                $('.attrs').each(function () {
                    var obj = $(this);
                    if (obj.val()) {
                        formData[obj.attr('name')] = obj.val();
                    }
                });
                data.formData = formData;
                data.submit();
            });
        },
        fail: function (e, data) {
            var json = data.jqXHR['responseJSON'], text = '文件上传失败';
            if (json && json['message']) {
                text = json['message'];
            }
            $(this).parent().after('<span class="fileinput-error"> ' + text + '</span>');
            alert(text);
        },
        done: function (e, data) {
            $(this).parent().after('<span class="fileinput-error"> 上传成功</span>');
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
 * 浮点数加法
 * @param arg1
 * @param arg2
 * @returns {number}
 */
var accAdd = function (arg1, arg2) {
    var r1, r2, m;
    try {
        r1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2));
    return (arg1.mul(m) + arg2.mul(m)) / m;
}


/**
 * 浮点数减法
 * @param arg1
 * @param arg2
 * @returns {number}
 */
var Subtr = function (arg1, arg2) {
    var r1, r2, m, n;
    try {
        r1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
        r1 = 0;
    }
    try {
        r2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
        r2 = 0;
    }
    m = Math.pow(10, Math.max(r1, r2));
    //last modify by deeka
    //动态控制精度长度
    n = (r1 >= r2) ? r1 : r2;
    return ((arg1.mul(m) - arg2.mul(m)) / m).toFixed(n);
}

/**
 * 浮点数乘法
 * @param arg1
 * @param arg2
 * @returns {number}
 */
var accMul = function (arg1, arg2) {
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length;
    }
    catch (e) {
    }
    try {
        m += s2.split(".")[1].length;
    }
    catch (e) {
    }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}

/**
 * 浮点数除法
 * @param arg1
 * @param arg2
 * @returns {number}
 */
var accDiv = function (arg1, arg2) {
    var t1 = 0, t2 = 0, r1, r2;
    try {
        t1 = arg1.toString().split(".")[1].length;
    }
    catch (e) {
    }
    try {
        t2 = arg2.toString().split(".")[1].length;
    }
    catch (e) {
    }
    with (Math) {
        r1 = Number(arg1.toString().replace(".", ""));
        r2 = Number(arg2.toString().replace(".", ""));
        return (r1 / r2) * pow(10, t2 - t1);
    }
}

//给Number类型增加一个add方法，，使用时直接用 .add 即可完成计算。
Number.prototype.add = function (arg) {
    return accAdd(arg, this);
};

//给Number类型增加一个add方法，，使用时直接用 .sub 即可完成计算。
Number.prototype.sub = function (arg) {
    return Subtr(this, arg);
};

//给Number类型增加一个mul方法，使用时直接用 .mul 即可完成计算。
Number.prototype.mul = function (arg) {
    return accMul(arg, this);
};

//给Number类型增加一个div方法，，使用时直接用 .div 即可完成计算。
Number.prototype.div = function (arg) {
    return accDiv(this, arg);
};
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