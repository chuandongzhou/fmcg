@include('includes.quick-link')

<!--右侧贴边导航quick_links.js控制-->
<div class="mui-mbar-tabs">
    <div class="quick_link_mian">
        <div class="quick_links_panel">
            <div id="quick_links" class="quick_links">
                <li>
                    <a href="#" class="my_qlinks"><i class="setting"></i></a>

                    <div class="ibar_login_box status_login">
                        <div class="avatar_box">
                            <p class="avatar_imgbox"><img src="http://placehold.it/100"/></p>
                            <ul class="user_info">
                                <li>用户名：sl19931003</li>
                                <li>级&nbsp;别：普通会员</li>
                            </ul>
                        </div>
                        <div class="login_btnbox">
                            <a href="#" class="login_order">我的订单</a>
                            <a href="#" class="login_favorite">我的收藏</a>
                        </div>
                        <i class="icon_arrow_white"></i>
                    </div>
                </li>
                <li id="shopCart">
                    <a href="#" class="cart"><i class="message"></i>

                        <div class="span">购物车</div>
                        <span class="cart_num">{{ $cartNum }}</span></a>
                </li>
                <li>
                    <a href="#" class="my_assets"><i class="view"></i></a>

                    <div class="mp_tooltip">我的资产<i class="icon_arrow_right_black"></i></div>
                </li>
                <li>
                    <a href="{{ asset('like/goods') }}" class="like" target="_blank"><i class="wdsc"></i></a>

                    <div class="mp_tooltip">我的收藏<i class="icon_arrow_right_black"></i></div>
                </li>
            </div>
            <div class="quick_toggle">
                <li><a href="#top" class="return_top"><i class="top"></i></a></li>
            </div>
        </div>
        <div id="quick_links_pop" class="quick_links_pop">

        </div>
    </div>
</div>
