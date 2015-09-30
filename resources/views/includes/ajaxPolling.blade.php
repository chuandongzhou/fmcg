<script>
    function getData() {//获取消息
        var targetUrl = '{{ url("order-buy/order-polling") }}';
        $.get( targetUrl, function (json) {//利用ajax返回json的方式
            console.log(json);
            var div = $('#alert-div');
            if(json.type == 'user'){
                div.attr('href','{{ url("order-buy/non-payment") }}');
                div.text('卖家确认了你的订单');
                div.fadeIn();
                setTimeout(function(){
                    div.fadeOut(3000);
                },6000);
            }
            if(json.type == 'shop'){
                div.attr('href','{{ url("order-sell/non-sure") }}');
                div.text('你有新的订单需要确认');
                div.fadeIn();
                setTimeout(function(){
                    div.fadeOut(3000);
                },6000);
            }
        });
    }
    $(function () {//间隔30s自动加载一次
            getData(); //首次立即加载
            window.setInterval(getData, 10000); //循环执行！！
        }
    );
</script>
