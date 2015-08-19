function onCheckChange(parent_selectors, target_selectors) {
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
 * 店铺添加图片处理
 */
function shopPicFunc() {
    var container = $('.shop-pictures')
        , uploadButton = $('#shop-pic-upload')
        , uploadLimit = 5;

    // 图片上传限制
    var changeUploadButtonStatus = function () {
        if (container.children('div').length >= uploadLimit) {
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
    $('#shop-pic-upload').on('cropped.hct.cropper', '', function (e, data) {
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
 * 添加地址
 */
function addAddFunc() {
    var container = $('.address-list')
        , addButton = $('#add-address')
        , btnAdd = $('.btn-add')
        , addLimit = 5, province = $('.add-province')
        , city = $('.add-city')
        , district = $('.add-district')
        , address = $('.address');

    $('#addressModal').on('hidden.bs.modal', function (e) {
        //TODO 初始化地址
        address.val('');
    });
    // 地址限制
    var changeAddButtonStatus = function () {
        if (container.children('div').length >= addLimit) {
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
        if (!district.val()) {
            changeBtnAddhtml('请选择区县...');
            return false;
        }
        if (!address.val()) {
            changeBtnAddhtml('请输入详细地址');
            return false;
        }
        var provinceText = province.find("option:selected").text(),
            cityText = city.find("option:selected").text(),
            districtText = district.find("option:selected").text(),
            addressText = address.val(),
            addressDetail = provinceText + ' ' + cityText + ' ' + districtText + ' ' + addressText + ' ';
        $('.btn-close').trigger('click');
        container.prepend(
            '<div class="col-sm-12 fa-border">' +
            addressDetail +
            '<span class="close">×</span>' +
            '<input type="hidden" name="delivery_area[]" value="' + addressDetail + '" />',
            '</div>'
        );
        changeAddButtonStatus();
    });
}

function getCategory(url) {
    var level1 = $('select[name="level1"]')
        , level2 = $('select[name="level2"]')
        , level3 = $('select[name="level3"]');


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
function getAllCategory(url, level1, level2, level3) {
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
        addOption(level1Info, $('select[name="level1"]'), level1);
        addOption(level2Info, $('select[name="level2"]'), level2, '<option value="">请选择</option>');
        addOption(level3Info, $('select[name="level3"]'), level3, '<option value="">请选择</option>');
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
