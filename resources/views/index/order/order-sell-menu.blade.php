<div class="col-sm-12 padding-clear">
    <div class="notice-bar clearfix">
        <a class="{{ \Request::is('order-sell') ? "active" : "" }}" href="{{ url('order-sell') }}">所有订单</a>
        <a class="{{ \Request::is('order-sell/wait-send') ? "active" : "" }}" href="{{ url('order-sell/wait-send') }}">待发货<span class="badge">{{ $data['nonSend'] or '' }}</span></a>
        <a class="{{ \Request::is('order-sell/wait-receive') ? "active" : "" }}" href="{{ url('order-sell/wait-receive') }}">待收款<span class="badge">{{ $data['waitReceive'] or '' }}</span></a>
        <a class="{{ \Request::is('order-sell/wait-confirm') ? "active" : "" }}" href="{{ url('order-sell/wait-confirm') }}">未确认<span class="badge">{{ $data['waitConfirm'] or '' }}</span></a>
    </div>
</div>