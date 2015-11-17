<div class="personal-center">
    <div class="col-sm-12 switching">
        <a href="{{ url('personal/shop') }}" class="btn {{ path_active('personal/shop') }}">商家信息</a>
        @if(auth()->user()->type <= cons('user.type.wholesaler'))
            <a href="{{ url('personal/shipping-address') }}"
               class="btn {{ path_active('personal/shipping-address*') }}">收货地址</a>
        @endif
        @if(auth()->user()->type >= cons('user.type.wholesaler'))
            <a href="{{ url('personal/balance') }}"
               class="btn {{ path_active(['personal/balance','personal/withdraw']) }}">账号余额</a>
            <a href="{{ url('personal/bank') }}"
               class="btn {{ path_active('personal/bank*') }}">提现账号</a>
            {{--<a href="#" class="btn">人员管理</a>--}}
            <a href="{{ url('personal/delivery-man') }}"
               class="btn {{ path_active('personal/delivery-man*') }}">配送人员</a>
        @endif
        <a href="{{ url('personal/password') }}" class="btn {{ path_active('personal/password') }}">修改密码</a>

    </div>
</div>