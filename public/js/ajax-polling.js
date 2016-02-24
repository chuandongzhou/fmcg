$(function () {//间隔30s自动加载一次
        //前台轮询结果处理
        function getPushData() {//获取消息
            var targetUrl = site.baseUrl + "/order/order-polling";
            $.get(targetUrl, function (json) {//利用ajax返回json的方式
                var div = $('.msg-channel'), check = div.find('.check');
                var uri = '';
                if (json.data != undefined) {
                    switch (json.type) {
                        case 'user':
                            uri = site.baseUrl + '/order-buy';
                            break;
                        case 'seller':
                            uri = site.baseUrl + '/order-sell';
                            break;
                        default :
                            uri = site.baseUrl + '/personal/withdraw';
                            break;
                    }
                    check.attr('href', uri);
                    check.html(json.data);
                    div.css('bottom', '5px');
                   /* setTimeout(function () {
                        div.css('display', 'none');
                    }, 6000);*/
                }
            });
        }

        getPushData(); //首次立即加载
        window.setInterval(getPushData, 10000); //循环执行！！
    }
);
