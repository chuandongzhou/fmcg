/**
 * 提现订单操作事件
 */
var withdrawOperateEvents = function () {
    $('.btn-add').prop('disabled', true);
    $('table').on('click', '.rollback', function () {
        var withdraw_id = $(this).attr('data-id');
        var reason = '';
        $('#rollback').on('keyup', 'textarea[name="reason"]', function () {
            reason = $(this).val();
            if (reason) {
                $('#rollback').find('.btn-add').prop('disabled', false).attr('data-data', '{"withdraw_id":' + withdraw_id + ',"reason":"' + reason + '"}');
            }
        });
    })
        .on('click', '.payment', function () {
            var withdraw_id = $(this).attr('data-id');
            var tradeNo = '';
            $('#payment').on('keyup', 'input[name="tradeNo"]', function () {
                tradeNo = $(this).val();
                if (tradeNo) {
                    $('#payment').find('.btn-add').prop('disabled', false).attr('data-data', '{"withdraw_id":' + withdraw_id + ',"trade_no":"' + tradeNo + '"}');
                }
            });
        });
}

/**
 * 走势图
 * @param obj
 * @param title
 * @param legend
 * @param xAxis
 * @param series
 */
var echartsSet = function (obj, title, legend, xAxis, series) {
    var myChart = echarts.init(obj),
        option = {
            title: {
                text: title
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: legend
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: xAxis
            },
            yAxis: {
                type: 'value'
            },
            series: series
        };
    myChart.setOption(option);
};

/**
 * 用户数据统计
 */
var userData = function () {
    $("[data-toggle='popover']").popover();
    var obj = $('#myChart')[0], obj1 = $('#myChart1')[0], beginDay = $('input[name="begin_day"]'), endDay = $('input[name="end_day"]');

    $.get(site.url('admin/operation-data/user-register'), {
        begin_day: beginDay.val(),
        end_day: endDay.val()
    }, function (data) {
        var legend = ['供应商', '批发商', '终端商'];
        echartsSet(obj, '用户注册数量统计', legend, data.created_at, [
            {
                name: legend[0],
                type: 'line',
                data: data.supplier_reg
            },
            {
                name: legend[1],
                type: 'line',
                data: data.wholesaler_reg
            },
            {
                name: legend[2],
                type: 'line',
                data: data.retailer_reg
            }
        ]);
        echartsSet(obj1, '用户登录数量统计', legend, data.created_at, [
            {
                name: legend[0],
                type: 'line',
                data: data.supplier_login
            },
            {
                name: legend[1],
                type: 'line',
                data: data.wholesaler_login
            },
            {
                name: legend[2],
                type: 'line',
                data: data.retailer_login
            }
        ]);
    }, 'json');

    $('.form-horizontal').on('click', '.export', function () {
        var url = site.url('admin/operation-data/user-export'),
            queryString = 'begin_day=' + beginDay.val() + '&end_day=' + endDay.val();
        window.location.href = url + '?' + queryString;
    })
};

/**
 * 金融数据统计
 */
var financial = function () {
    var obj = $('#myChart')[0], beginDay = $('input[name="begin_day"]'), endDay = $('input[name="end_day"]');
    $.get(site.url('admin/operation-data/order-create-map'), {
        begin_day: beginDay.val(),
        end_day: endDay.val()
    }, function (data) {
        var legend = ['批发商', '终端商'];
        echartsSet(obj, '下单金额统计', legend, data.dates, [
            {
                name: legend[0],
                type: 'line',
                data: data.wholesalerAmount
            },
            {
                name: legend[1],
                type: 'line',
                data: data.retailerAmount
            }
        ]);

    }, 'json');

}
