@section('footer')
    <div class="fixed-footer fixed-item nav-bottom">
        <div class="row">
            <a class="bottom-menu-item col-xs-3 btn {{ request()->is('/') ? 'on' : '' }}" href="{{ url('/') }}">
                <i class="iconfont icon-shouye"></i>首页
            </a>
            <a class="bottom-menu-item  col-xs-3 btn {{ request()->is('shop') ? 'on' : '' }}" href="{{ url('shop') }}">
                <i class="iconfont icon-shangpu"></i>商铺
            </a>
            <a class="bottom-menu-item  col-xs-3 btn {{ request()->is('cart') ? 'on' : '' }}" href="{{ url('cart') }}">
                <i class="iconfont icon-gouwuche">
                    <span class="badge">{{ $cartNum }}</span>
                </i>购物车
            </a>
            <a class="bottom-menu-item col-xs-3 btn {{ request()->is('mine') ? 'on' : '' }}" href="{{ url('mine') }}">
                <i class="iconfont icon-wode"></i>我的
            </a>
        </div>
    </div>
@stop