<div class="mask-outer">
    <div class="pop-general text-center" id="pop-pay">
        <div class="modal-header">
            <a class="fa fa-remove pull-right close-btn" onclick="popClose();"></a>
            <h4 class="modal-title text-center forgot-modal-title" id="myModalLabel2">
                登录网上银行支付
            </h4>
        </div>
        <div class="pop-content">
            <p class="pop-tips">请您在新打开的网上银行页面进行支付,支付完成前请不要关闭该窗口</p>

            <div class="pop-btn">
                <a href="{{ url('order-buy/detail?order_id=' . $orderId)}}"
                   class="btn pay-success btn-default">已完成支付</a>
                <a href="javascript:" onclick="popClose()" class="btn btn-default">支付未成功</a>
            </div>
        </div>
    </div>
</div>

@section('js')
    @parent
    <script type="text/javascript">
        function showPaySuccess() {
            var box = $('.mask-outer');
            box.css('display', 'block');
        }
        function popClose() {
            $('.mask-outer').css("display", "none");
        }
    </script>
@stop