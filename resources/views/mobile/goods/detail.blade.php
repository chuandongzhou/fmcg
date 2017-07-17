@extends('mobile.master')

@section('subtitle', '商品列表')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item commodity-details-nav">
        <div class="row margin-clear">
            <div class="col-xs-2 text-right">
                <a class="iconfont icon-fanhui2 go-back" onclick="window.history.back()"></a>
            </div>
            <div class="col-xs-10 text-right nav-shopping-cart">
                <a class="iconfont icon-gouwuche" href="{{ url('cart') }}"><span class="badge">{{ $cartNum }}</span></a>
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m60 p65">
        <div class="row ">
            <div class="col-xs-12 pd-clear">
                <img src="{{ $goods->image_url }}" class="details-banner"/>
            </div>
        </div>
        <div class="row commodity-details-item">
            <div class="col-xs-12 item commodity-name">{{ $goods->name }}</div>
            <div class="col-xs-6 item red">¥{{ $goods->price . '/' . $goods->pieces }}</div>
            <div class="col-xs-6 item text-right">
                <a href="javascript:" data-type="goods" data-method="post"
                   class="bottom-menu-item btn btn-like list-name like-shops"
                   data-id="{{ $goods->id }}">
                    @if($goods->is_like)
                        <i class="fa fa-star"></i> 已收藏
                    @else
                        <i class="fa fa-star-o"></i> 加入收藏夹
                    @endif
                </a>
            </div>
            <div class="col-xs-6 item prompt"> 累计销量 : {{ $goods->sales_volume }}</div>
            <div class="col-xs-6 item text-right"><span class="prompt">最低购买数 : </span>{{ $goods->min_num }}</div>
        </div>
        <div class="row white-bg commodity-details-item">
            <div class="col-xs-6 item">自提价 : ¥{{ $goods->pick_up_price . '/' . $goods->pieces }}</div>
            <div class="col-xs-6 item text-right">新品 : {{ $goods->is_new ? '是' : '否' }}</div>
            <div class="col-xs-6 item ">退换货
                : {{ $goods->is_back ? '可退货' : '' }}  {{  $goods->is_change ? '可换货' : ($goods->is_back ? '' : '不可退 不可换')  }}</div>
            <div class="col-xs-6 item text-right">规格 : {{ $goods->specification or '暂无' }}</div>
            <div class="col-xs-6 item ">保质期 : {{ $goods->shelf_life or '- -' }}</div>
            <div class="col-xs-6 item text-right">即期品 : {{ $goods->is_expire ? '是' : '否' }}</div>
        </div>
        <div class="row shop-entrance white-bg">
            <div class="col-xs-12 pd-clear">
                <i class="iconfont icon-shangpu prompt"></i>
                <span class="shop-name">{{ $goods->shop_name }}</span>
                <span class="prompt role">（{{ cons()->valueLang('user.type' , $goods->shop->user_type) }}）</span>
            </div>
        </div>
        <div class="row white-bg tab-container">
            <div class="col-xs-12 tab-wrap clearfix">
                <div class="item pull-left"><a class="active" id="tuwen">图文详情</a></div>
                <div class="item pull-left"><a id="address">配送地区</a></div>
            </div>
            <div class="col-xs-12 tuwen-content content-item pd-clear">
                {!! $goods->introduce !!}
            </div>
            <div class="col-xs-12 address-content content-item pd-clear hidden">
                <div class="item title">配送地区</div>
                @foreach($goods->deliveryArea as $area)
                    <div class="item td">{{ $area->address_name }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <!--弹出层-->
    <div class="popover-wrap popover-role">
        <div class="popover-panel">
            <div class="title text-center">请填写购买数量</div>
            <div class="add-num">
                <input type="text" class="enter-num num" maxlength="5" data-min-num="{{ $goods->min_num }}"
                       value="{{ $goods->min_num }}"/>
            </div>
        </div>
    </div>
@stop
@section('footer')
    <div class="fixed-footer fixed-item nav-bottom pd-clear">
        <div class="row">
            <a class="bottom-menu-item col-xs-4 btn" href="{{ url('shop/' . $goods->shop_id) }}">
                <i class="iconfont icon-shangpu"></i>
                <div class="item-name">进入商铺</div>
            </a>
            <div class="col-xs-4 add-shopping-num pd-clear">
                <a class="red"><i class="iconfont icon-jia1"></i><span>{{ $goods->min_num }}</span></a>
            </div>
            <div class="col-xs-4 join-cart pd-clear">
                <button type="button" data-url="{{ url('api/v1/cart/add/'.$goods->id) }}" class="btn btn-primary add-cart">加入购物车</button>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $(".tab-wrap .item a").click(function () {
                $(this).addClass("active").parents(".item").siblings().children("a").removeClass("active");
                $("." + $(this).attr("id") + "-content").removeClass("hidden").siblings(".content-item").addClass("hidden");
            });
            var cartNum = $('.popover-wrap').find('.num').data('min-num');
            //点击出弹出层
            $(".add-shopping-num a").click(function () {
                layer.open({
                    title: false,
                    content: $(".popover-role").html(),
                    style: ' width:95%; height: auto;  padding:0;',
                    shade: 'background-color: rgba(0,0,0,.3)',
                    btn: ['确定', '取消'],
                    yes: function (index) {
                        var cartNumInput = $('.layui-m-layerchild').find('.num'),
                            minNum = parseInt(cartNumInput.data('min-num'));
                        cartNum = parseInt(cartNumInput.val()),
                            cartNum = cartNum >= minNum ? cartNum : minNum;
                        $('.add-shopping-num span').html(cartNum);
                        layer.close(index)
                    },
                    success: function () {
                        $(".enter-num").val($('.add-shopping-num span').html())
                    }
                });
                $(".popover-panel").parent().addClass("pd-clear");

            });

            //加入购物车
            $('.add-cart').on('click', function () {
                var obj = $(this), url = obj.data('url');
                obj.button({
                    loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
                    doneText: '操作成功',
                    failText: '操作失败'
                });
                obj.button('loading');
                $.ajax({
                    url: url,
                    method: 'post',
                    data: {num: cartNum}
                }).done(function () {
                    obj.button('done');
                    showMassage('加入成功')
                }).fail(function (jqXHR) {
                    obj.button('fail');
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {
                            showMassage(json['message']);
                        }, 0);
                    }
                }).always(function () {
                    setTimeout(function () {
                        obj.button('reset');
                    }, 2000);
                });

                return false;
            });
            likeFunc();
        })
    </script>
@stop