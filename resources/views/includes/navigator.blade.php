@include('includes.quick-link')

        <!--右侧贴边导航quick_links.js控制-->
<div class="quick-wrap">
    <div class="quick_links_panel">
        <div id="quick_links" class="quick_links">
            <li>
                <a href="javascript:;" class="my_qlinks"><i class="setting"></i></a>
                @if(isset($user))
                    <div class="ibar_login_box status_login">
                        <div class="avatar_box">
                            <p class="avatar_imgbox"><img src="{{ $user->shop->logo_url }}"/></p>
                            <ul class="user_info">
                                <li>店铺名：{{ $user->shop_name }}</li>
                                <li>类　型：{{ cons()->valueLang('user.type' , $user->type) }}</li>
                            </ul>
                        </div>
                        <div class="login_btnbox">
                            <a href="{{ isset($user) && $user->type > cons('user.type.retailer') ? url('order-sell') : url('order-buy') }}"
                               class="login_order">我的订单</a>
                            <a href="{{ url('like/goods') }}" class="login_favorite">我的收藏</a>
                        </div>
                        <i class="icon_arrow_white"></i>
                    </div>
                @endif
            </li>
            <li id="shopCart">
                <a href="javascript:;" class="message_list pop-show-link"><i class="message"></i>

                    <div class="span">购物车</div>
                    <span class="cart_num">{{ $cartNum }}</span></a>
            </li>
            <li id="coupon">
                <a href="javascript:;" class="history_list pop-show-link"><i class="view"></i></a>

                <div class="mp_tooltip" style=" visibility:hidden;">我的资产<i class="icon_arrow_right_black"></i>
                </div>
            </li>
            <li>
                <a href="{{ url('like/goods') }}" class="mpbtn_wdsc"><i class="wdsc"></i></a>

                <div class="mp_tooltip">我的收藏<i class="icon_arrow_right_black"></i></div>
            </li>
        </div>
        <div class="quick_toggle">
            <li><a href="javascript:;" class="return_top"><i class="top"></i></a></li>
        </div>
    </div>
    <div id="quick_links_pop" class="quick_links_pop ">

    </div>
</div>
