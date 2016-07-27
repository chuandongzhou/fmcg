<div class="col-sm-12 ">
    <div class="notice-bar">
        <a class="btn {{ \Request::is('order-buy') ? "btn-primary" : "" }}" href="{{ url('order-buy') }}">所有订单</a>
        <a class="btn {{ \Request::is('order-buy/wait-pay') ? "btn-primary" : "" }}" href="{{ url('order-buy/wait-pay') }}">待付款{{ $data['waitPay'] or '' }}</a>
        <a class="btn {{ \Request::is('order-buy/wait-receive') ? "btn-primary" : "" }}" href="{{ url('order-buy/wait-receive') }}">待收货{{ $data['waitReceive'] or '' }}</a>
        <a class="btn {{ \Request::is('order-buy/wait-confirm') ? "btn-primary" : "" }}" href="{{ url('order-buy/wait-confirm') }}">待确认{{ $data['waitConfirm'] or '' }}</a>
    </div>
</div>