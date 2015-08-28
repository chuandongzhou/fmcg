/**
 * 动态查询订单列表
 */
function ajaxGetSelect() {
    $('.ajax-get').on('change', function () {
        _getArgs();
    });
    $('.datetimepicker').on('blur', function () {
        _getArgs();
    });
}
function _getArgs(){
    data = {};
    targetUrl = $('#target-url').val();
    //拼装select条件
    $('select.ajax-get').each(function () {
        var key = $(this).attr('name');
        var value = $(this).find('option:selected').val();
        data[key] = value;
    });
    //拼装时间条件
    $('input.datetimepicker').each(function () {
        var key = $(this).attr('name');
        var value =  $(this).val();

        data[key] = value;
    });
    if(data['start_at'] > data['end_at']){
        return false;
    }
    $.ajax({
        type: targetUrl,
        url: targetUrl,
        data :data,
        dataType: 'json',
        success: function (data) {
            alert(data);
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
