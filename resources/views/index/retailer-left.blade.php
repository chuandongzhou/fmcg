<div class="col-sm-2 menu">
    <ul class="name" href="#">
        <li><img class="avatar" src="http://placehold.it/50"></li>
        <li>终端商名称</li>
    </ul>
    <ul class="menu-list dealer-menu-list">
        <li>
            <a href="#" class="list-item"><i class="fa fa-star-o"></i> 我的收藏</a>
            <ul class="menu-wrap">
                <li><a href="{{ url('collect/shops') }}">店铺收藏</a></li>
                <li><a href="{{ url('collect/goods') }}">商品收藏</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ url('order-buy') }}" class="list-item"><i class="fa fa-file-text-o"></i> 我的订单</a>
        </li>
        <li><a href="{{ url('order-buy/statistics') }}" class="list-item active"><i class="fa fa-file-o"></i>
                统计报表</a></li>
        <li><a href="#" class="list-item"><i class="fa fa-heart-o"></i> 个人中心</a></li>
    </ul>
</div>