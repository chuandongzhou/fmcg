@extends('index.index-master')
@include('includes.loadMapJs')
@section('container')
    <div class="container wholesalers-index goods-detail">
        <div class="row">
            <div class="col-sm-5 left-store-logo">
                <div id="myCarousel" class="carousel slide banner-slide">
                    <ol class="carousel-indicators">
                        @foreach($goods->images_url as $key =>$image)
                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}">
                        @endforeach
                    </ol>
                    <div class="carousel-inner banner">
                        @foreach($goods->images_url as $key =>$image)
                            <div class="item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-sm-7 store-detail-wrap">
                <h3 class="store-name">{{ $goods->name }}</h3>

                <div class="clearfix store-detail">
                    <ul class="pull-left left-panel">
                        <li><span class="title-name">价格 </span><b>￥{{ $goods->price }}</b></li>
                        @foreach($attrs as $key=>$attr)
                            <li>
                                <span class="title-name">{{ $key }} </span><b>{{ $attr }}</b>
                            </li>
                        @endforeach

                        <li><span class="title-name">累计销售量 </span><b>{{ $goods->sales_volume }}</b></li>
                    </ul>
                    <ul class="pull-left right-panel">
                        @if($goods->is_promotion)
                            <li><span class="title-name">促销信息 </span> : <b>{{ $goods->promotion_info }}</b></li>
                        @endif
                        <li>
                            <span class="title-name">退换货 </span><b>{{ $goods->is_back ? '可退货' : '' }}  {{  $goods->is_change ? '可换货' : ''  }}</b>
                        </li>
                        <li>
                            <span class="title-name">即期品 </span><b>{{ cons()->valueLang('goods.type' ,$goods->is_expire ) }}</b>
                        </li>
                        <li><span class="title-name">商家 </span><b>{{ $goods->shop->name }}</b></li>
                        <form action="{{ url('cart/add') }}" class="form-horizontal  ajax-form" method="post">
                            <li>
                                <button disabled class="btn count btn-cancel desc-num">-</button>
                                <input type="text" class="amount num" name="num" value="{{ $goods->min_num }}">
                                <button class="btn count btn-cancel inc-num">+</button>
                                <span class="title-name">最底购买量:{{ $goods->min_num }}</span>
                            </li>
                            <li>
                                <a href="javascript:void(0)" data-url="{{ url('api/v1/cart/add/'.$goods->id) }}"
                                   class="btn btn-primary add-to-cart">加入购物车</a>

                                <a href="javascript:void(0)" data-type="goods" data-method="post"
                                   class="btn btn-default btn-like" data-id="{{ $goods->id }}">
                                    @if(is_null($isLike))
                                        <i class="fa fa-star-o"></i> 加入收藏夹
                                    @else
                                        <i class="fa fa-star"></i> 已收藏
                                    @endif
                                </a>
                                <a href="javascript:history.back()" class="btn btn-cancel submit-order">返回</a>
                            </li>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row nav-wrap">
            <div class="col-sm-12 switching">
                <a href="javascript:void(0)" id="location" class="active">配送区域</a>
                <a href="javascript:void(0)" id="graphic-details">图文详情</a>
            </div>
            <div class="col-sm-12 address-wrap  location box active">
                <div class="item clearfix">
                    <h5 class="title-name">商品配送区域 :</h5>
                    <ul class="address-list">
                        @foreach($goods->deliveryArea as $area)
                            <p class="col-sm-12">{{ $area->address_name }}</p>
                        @endforeach
                    </ul>
                </div>
                <div class="item">
                    <h5 class="title-name">商品配送区域大概地图标识 :</h5>

                    <div id="map"></div>
                </div>
            </div>
            <div class="col-sm-12 box graphic-details">
                {!!$goods->introduce !!}
            </div>

        </div>
    </div>

    <!--弹出层-->
    <div class="mask-outer">
        <div class="pop-general text-center">
            <div class="pop-content">
                <a class="fa fa-close pull-right close-btn" href="javascript:void(0)"></a>

                <p class="pop-tips"><i class="fa fa-check-circle-o"></i><span class="txt">已成功加入购物车</span></p>

                <div class="pop-btn">
                    <a href='javascript:history.back()' class="btn btn-default">继续购物</a>
                    <a href="{{ url('cart') }}" class="btn btn-danger">查看购物车</a>
                </div>
            </div>
        </div>
    </div>


@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            });
            $('.add-to-cart'). on('click', function () {
                var obj = $(this), url = obj.data('url'), buyNum = $('input[name="num"]').val();
                obj.button({
                    loadingText: '<i class="fa fa-spinner fa-pulse"></i> 操作中...',
                    doneText: '操作成功',
                    failText: '操作失败'
                });
                obj.button('loading')
                $.ajax({
                    url: url,
                    method: 'post',
                    data: {num: buyNum}
                }).done(function () {
                    obj.button('done');
                }).fail(function (jqXHR) {
                    obj.button('fail');
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {
                            obj.html(json['message']);
                        }, 0);
                    }
                }).always(function () {
                    $(".mask-outer").css("display", "block");
                });

                return false;
            });
            $('a.close-btn').on('click' , function (){
                $(".mask-outer").css("display","none");
            });
            numChange({{ $goods->min_num }});
            tabBox();
            likeFunc();
            getCoordinateMap({!! $coordinates !!});
        });
    </script>
@stop