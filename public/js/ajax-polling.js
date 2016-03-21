$(function () {//间隔30s自动加载一次
        //前台轮询结果处理
        function getPushData() {//获取消息
            var targetUrl = site.baseUrl + "/order/order-polling";
            if(!SITE.USER.id) {
                return false;
            }
            $.ajax(
                {
                    url: targetUrl,
                    success: function (json) {//利用ajax返回json的方式
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
                            $("#myaudio")[0].play();
                        }
                    },
                    error:function(){
                       window.clearInterval(timer);
                    }
                })
            ;
        }

        getPushData(); //首次立即加载
        var timer = window.setInterval(getPushData, 20000); //循环执行！！
    }
);
