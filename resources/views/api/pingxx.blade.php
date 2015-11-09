<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<h1>模拟POST发送页面 www.bpicms.com</h1>
-开发用

订单号:<input class="order_id"/><button class="get-charge">获取charge对象</button><br/>

Charge:<br/>

<textarea class="charge" style="width:600px; height:100px"></textarea>
<input type="button" class="submit" value="提交"/>
<br/>
<br/>
<br/>
</body>
</html>

<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/pingpp-pc.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('.get-charge').click(function () {
            var orderId = $('.order_id').val(), chargeArea = $('textarea');
            $.get('/api/v1/pay/charge/' + orderId, '', function (data) {
                var data = JSON.stringify(data);
                chargeArea.val(data)
            },'json');
        })
        $('.submit').click(function(){
            var charge = $('.charge').val();

            pingpp.createPayment(charge, function(result, err){
                alert(err);
            });
        })
    })
</script>
