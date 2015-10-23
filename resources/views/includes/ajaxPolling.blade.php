<script>
    function getData() {//获取消息
        var targetUrl = '{{ url("order-buy/order-polling") }}';
        $.get( targetUrl, function (json) {//利用ajax返回json的方式
            var div = $('#alert-div');
            if(json.type == 'user'){
                div.attr('href','{{ url("order-buy") }}');
                div.text(json.data);
                div.fadeIn();
                setTimeout(function(){
                    div.fadeOut(3000);
                },6000);

            }
            if(json.type == 'seller'){
                div.attr('href','{{ url("order-sell") }}');
                div.text(json.data);
                div.fadeIn();
                setTimeout(function(){
                    div.fadeOut(3000);
                },6000);
            }

        });
    }
    $(function () {//间隔30s自动加载一次
            getData(); //首次立即加载
            window.setInterval(getData, 100000); //循环执行！！
        }
    );
</script>
