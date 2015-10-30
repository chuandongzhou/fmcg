<script>
    $(function () {//间隔30s自动加载一次
            //前台轮询结果处理
            function getPushData() {//获取消息
                var targetUrl = '{{ url("order-buy/order-polling") }}';
                $.get(targetUrl, function (json) {//利用ajax返回json的方式
                    var div = $('#alert-div');
                    var uri = '';
                    console.log(json.type);
                    if (json.data != undefined) {
                        switch (json.type) {
                            case 'user':
                                uri = '{{ url("order-buy") }}';
                                break;
                            case 'seller':
                                uri = '{{ url("order-sell") }}';
                                break;
                            default :
                                uri = '{{ url("personal/withdraw") }}';
                                break;
                        }
                        div.attr('href', uri);
                        div.text(json.data);
                        div.fadeIn();
                        setTimeout(function () {
                            div.fadeOut(3000);
                        }, 6000);
                    }
                });
            }

            getPushData(); //首次立即加载
            window.setInterval(getPushData, 5000); //循环执行！！
        }
    );
</script>
