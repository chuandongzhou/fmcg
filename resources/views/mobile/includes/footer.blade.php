@section('footer')
    <div class="fixed-footer fixed-item nav-bottom">
        <a class="bottom-menu-item btn {{ request()->is('/') ? 'on' : '' }}" href="{{ url('/') }}">
            <i class="iconfont icon-shouye"></i>首页
        </a>
        <a class="bottom-menu-item btn {{ request()->is('shop') ? 'on' : '' }}" href="{{ url('shop') }}">
            <i class="iconfont icon-shangpu"></i>商铺
        </a>
        <a class="bottom-menu-item btn {{ request()->is('cart') ? 'on' : '' }}" href="{{ url('cart') }}">
            <i class="iconfont icon-gouwuche">
                <span class="badge">{{ $cartNum }}</span>
            </i>购物车
        </a>
        <a class="bottom-menu-item btn {{ request()->is('mine') ? 'on' : '' }}" href="{{ url('mine') }}">
            <i class="iconfont icon-wode"></i>我的
        </a>
    </div>
@stop