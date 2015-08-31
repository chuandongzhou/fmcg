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
        //查询对象
        var role = $('#search-role').val();
        if (value == '') {
            return false;
        }
        data['search_content'] = value;
        data['search_role'] = role;
        _ajaxGet(targetUrl, data);
    });
    //待办事项动态加载详情
    $('a.ajax-get').on('click',function(){
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
        data :data,
        dataType: 'json',
        success: function (list) {

            var str = '';

            $.each(list.data, function (index, result) {
                str += '<div class="row order-form-list">'
                    + '     <div class="col-sm-12 list-title">'
                    + '         <input type="checkbox" name="orderIds[]" value="' + result.id + '"/>'
                    + '         <span class="time">' + result.created_at + '</span>'
                    + '         <span>订单号:100000000' + result.id + '</span>'
                    + '         <span>' + (result.seller ? result.seller.user_name : result.user.user_name) + '终端商</span>'
                    + '     </div>'
                    + '     <div class="col-sm-8 list-content">'
                    + '         <ul>';
                $.each(result.goods, function (key, item) {
                    str += '             <li>'
                        + '                 <img src="' + item.image_url + '">'
                        + '                 <a class="product-name" href="#">' + item.name + '</a>'
                        + '                 <span class="red">￥' + item.pivot.price + '</span>'
                        + '                 <span>' + item.pivot.num + '</span>'
                        + '             </li>';
                });

                str += '         </ul>'
                    + '     </div>'
                    + '     <div class="col-sm-2 order-form-detail">'
                    + '         <p>订单状态 :' + result.status_name + '</p>'
                    + '         <p>支付方式 :' + result.payment_type + '</p>'
                    + '         <p>订单金额 :<span class="red">￥' + result.price + '</span></p>'
                    + '     </div>'
                    + '     <div class="col-sm-2 order-form-operating">'
                    + '         <p><a href="#" class="btn btn-primary">查看</a></p>'
                    + '         <p><a class="btn btn-danger ajax" data-method="put" data-url="'+SITE.ROOT+'/order/sure/'+ result.id +'">确认</a></p>'
                    + '         <p><a href="#" class="btn btn-success">导出</a></p>'
                    + '     </div>'
                    + '</div>';

            });
            $('.content').html(str);
        }
    });
}
function tabBox(){
    $(".switching a").click(function(){
        $(this).addClass("active").siblings().removeClass("active");
        var boxclass= $(this).attr("id");
        $("."+boxclass).css("display","block").siblings(".box").css("display","none");
    })
}
