<!--角色选择 弹出层-->
<div class="popover-wrap popover-role">
    <div class="popover-panel">
        <div class="title text-center">选择</div>
        <ul class="select-role-wrap">
            <li>
                <a href="javascript:;" data-type="{{ cons('user.type.retailer') }}">
                    <img src="{{ asset('images/mobile-images/role_1.png') }}">我是终端商
                    <i class="iconfont icon-jiantouyoujiantou"></i>
                </a>
            </li>
            <li>
                <a href="javascript:;" data-type="{{ cons('user.type.wholesaler') }}">
                    <img src="{{ asset('images/mobile-images/role_2.png') }}">我是批发商
                    <i class="iconfont icon-jiantouyoujiantou"></i>
                </a>
            </li>
            {{--<li>--}}
                {{--<a href="javascript:;" data-type="{{ cons('user.type.supplier') }}">--}}
                    {{--<img src="{{ asset('images/mobile-images/role_3.png') }}">我是供应商--}}
                    {{--<i class="iconfont icon-jiantouyoujiantou"></i>--}}
                {{--</a>--}}
            {{--</li>--}}
        </ul>
    </div>
</div>