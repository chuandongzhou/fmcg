<div class="col-sm-12 notice-bar">
    <a class="btn {{ \Request::is('order-sell') ? "btn-primary" : "" }}" href="{{ url('order-sell') }}">所有订单</a>
    <a class="btn {{ \Request::is('order-sell/wait-send') ? "btn-primary" : "" }}" href="{{ url('order-sell/wait-send') }}">待发货{{ $data['nonSend'] or '' }}</a>
    <a class="btn {{ \Request::is('order-sell/wait-receive') ? "btn-primary" : "" }}" href="{{ url('order-sell/wait-receive') }}">待收款{{ $data['waitReceive'] or '' }}</a>
    <a class="btn {{ \Request::is('order-sell/wait-confirm') ? "btn-primary" : "" }}" href="{{ url('order-sell/wait-confirm') }}">待确认{{ $data['waitConfirm'] or '' }}</a>
</div>