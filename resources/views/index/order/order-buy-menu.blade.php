<div class="col-sm-12  padding-clear">
    <div class="notice-bar clearfix">
        <a class="{{ \Request::is('order-buy') ? "active" : "" }}" href="{{ url('order-buy') }}">所有订单</a>
        <a class="{{ \Request::is('order-buy/wait-pay') ? "active" : "" }}" href="{{ url('order-buy/wait-pay') }}">待付款<span class="badge">{{ $data['waitPay'] or '' }}</span></a>
        <a class="{{ \Request::is('order-buy/wait-receive') ? "active" : "" }}" href="{{ url('order-buy/wait-receive') }}">待收货<span class="badge">{{ $data['waitReceive'] or '' }}</span></a>
        <a class="{{ \Request::is('order-buy/wait-confirm') ? "active" : "" }}" href="{{ url('order-buy/wait-confirm') }}">待确认<span class="badge">{{ $data['waitConfirm'] or '' }}</span></a>
    </div>
</div>