<div class="col-sm-12 padding-clear">
    <div class="notice-bar clearfix">
        <a class="{{ \Request::is('child-user/order') ? "active" : "" }}" href="{{ url('child-user/order') }}">所有订单</a>
        <a class="{{ \Request::is('child-user/order/wait-send') ? "active" : "" }}" href="{{ url('child-user/order/wait-send') }}">待发货<span class="badge">{{ $data['nonSend'] or '' }}</span></a>
        <a class="{{ \Request::is('child-user/order/wait-receive') ? "active" : "" }}" href="{{ url('child-user/order/wait-receive') }}">待收款<span class="badge">{{ $data['waitReceive'] or '' }}</span></a>
        <a class="{{ \Request::is('child-user/order/wait-confirm') ? "active" : "" }}" href="{{ url('child-user/order/wait-confirm') }}">未确认<span class="badge">{{ $data['waitConfirm'] or '' }}</span></a>
    </div>
</div>