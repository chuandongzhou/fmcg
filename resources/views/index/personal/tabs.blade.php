<div class="personal-center">
    <div class="col-sm-12 switching">
        <a href="{{ url('personal/shop') }}" class="btn {{ path_active('personal/shop') }}">商家信息</a>
        @if(auth()->user()->type <= cons('user.type.wholesaler'))
            <a href="{{ url('personal/shipping-address') }}"
               class="btn {{ path_active('personal/shipping-address') }} {{ path_active('personal/shipping-address/create') }}
               {{ path_active('personal/shipping-address/'.(isset($shippingAddress)&&is_object($shippingAddress) ? $shippingAddress->id : 0).'/edit') }}
                   ">收货地址</a>
        @endif
        @if(auth()->user()->type >= cons('user.type.wholesaler'))
            <a href="{{ url('personal/balance') }}"
               class="btn {{ path_active('personal/balance') }} {{ path_active('personal/withdraw') }}">账号余额</a>
            <a href="{{ url('personal/bank') }}"
               class="btn {{ path_active('personal/bank') }} {{ path_active('personal/bank/create') }}">提现账号</a>
            <a href="#" class="btn">人员管理</a>
            <a href="{{ url('personal/delivery-man') }}"
               class="btn {{ path_active('personal/delivery-man') }} {{ path_active('personal/delivery-man/create') }}
               {{ path_active('personal/delivery-man/'.(isset($deliveryMan)&&is_object($deliveryMan) ? $deliveryMan->id : 0).'/edit') }}
                   ">配送人员</a>

        @endif

        <a href="{{ url('personal/password') }}" class="btn {{ path_active('personal/password') }}">修改密码</a>

    </div>
</div>