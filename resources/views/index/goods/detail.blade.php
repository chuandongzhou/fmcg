@extends('index.master')
@include('includes.jquery-lazeload')
@section('subtitle')
    {{ $goods->name }}
@stop
@section('container')
    @include('includes.shop-search')
    <div class="container wholesalers-index goods-detail">
        <div class="row">
            <div class="col-sm-5 goods-detail-banner">

                <div class="classify">
                    @foreach($categoriesName as $key=>$cate)
                        @if($cate == $categoriesName->first())
                            <span>{{ $cate['name'] }}</span>>
                        @else
                            <a href="{{ url('search?category_id=' . $cate['level'] . $cate['id']) }}" target="_blank">
                                {{ $cate['name'] }}
                            </a>
                            {{ $cate == $categoriesName->last() ? '' : ' > ' }}
                        @endif

                    @endforeach
                </div>
                <div id="myCarousel" class="carousel slide banner-slide">
                    <ol class="carousel-indicators">
                        @foreach($goods->images_url as $key =>$image)
                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}">
                            </li>
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
            <div class="col-sm-4 store-detail-wrap pd-left-clear">
                <div class="store-title">
                    {{ $goods->name }}
                </div>
                <ul class="store-detail">
                    <li><span class="title-name">价格 : </span><span class="red"><span
                                    class="money">¥{{ $goods->price }}</span>/{{  $goods->pieces }}</span></li>
                    <li><span class="title-name">商品条形码 : </span>{{ $goods->bar_code }}</li>
                    <li><span class="title-name">商品ID : </span>{{ $goods->id }}</li>
                    <li><span class="title-name">保质期 : </span>{{ $goods->shelf_life}}</li>
                    <li><span class="title-name">规格 : </span>{{ $goods->specification or '暂无' }}</li>
                    <li>
                        <span class="title-name">退换货 : </span>{{ $goods->is_back ? '可退货' : '' }}  {{  $goods->is_change ? '可换货' : ($goods->is_back ? '' : '不可退 不可换')  }}
                    </li>
                </ul>
                <div class="item clearfix">
                    <span class="prompt label-name pull-left">数量 : </span>
                    <div class="pull-left num-wrap">
                        <input type="text" class="amount num pull-left" name="num" value="{{ $goods->min_num }}"
                               data-min-num="{{ $goods->min_num }}" data-max-num="{{ $goods->max_num }}">
                        <span class="count-operation pull-left">
                            <input class=" count desc-num pull-left desc-num" type="button" value="-" disabled><br>
                            <input class=" count inc-num pull-right inc-num" type="button" value="+">
                        </span>
                        <span class="minimum"><span class="prompt">最低购买量:</span> {{ $goods->min_num }}</span>
                    </div>
                </div>

                <div class="item">
                    <div class="pull-left">
                        @if($goods->is_out)
                            <a href="javascript:void(0)" class="btn shopping-btn disabled" disabled="">缺货</a>
                        @else
                            <a href="javascript:void(0)"
                               data-url="{{  $isMyGoods?'':url('api/v1/cart/add/'.$goods->id) }}"
                               class="btn shopping-btn join-cart {{ $isMyGoods?'disabled':'' }}">加入购物车</a>
                        @endif
                        <a class="btn shopping-btn {{ $isMyGoods?'disabled':'' }}"
                           href="{{ url('cart') }}">去购物车结算</a>
                    </div>
                    <div class="pull-right collect-item {{ $isMyGoods?'':'btn-like' }} like-goods"
                         data-type="goods" data-method="post"
                         data-id="{{ $goods->id }}" style="cursor:{{ $isMyGoods?'auto':'pointer' }}">
                        @if(is_null($isGoodsLike))
                            <i class="fa fa-star-o"></i> 收藏本商品
                        @else
                            <i class="fa fa-star"></i> 已收藏
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-3 shop-detail-wrap">
                <ul>
                    <li class="shop-name"><a href="{{ url('shop/'.$shop->id) }}" target="_blank">{{ $shop->name }}</a>
                    </li>
                    <li><img class="shop-img" src="{{ $shop->logo_url }}"></li>
                    <li><span class="prompt">联系人 : </span>{{ $shop->contact_person  }}</li>
                    <li><span class="prompt">联系方式 : </span>{{ $shop->contact_info }}</li>
                    <li><span class="prompt">最低配送额 : </span>¥{{ $shop->min_money }}</li>
                    <li><span class="prompt">店家地址 : </span>{{ $shop->address }}</li>
                </ul>
                <div class="operate">
                    @if($isMyGoods)
                        <a href="javascript:" class="contact list-name" style="cursor:text"><span
                                    class="fa fa-commenting-o"></span> 联系客服</a>
                    @else
                        <a href="javascript:"
                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                           class="contact list-name"><span class=" iconfont icon-kefu"></span> 联系客服</a>
                    @endif

                    <a style="cursor:{{ $isMyGoods?'auto':'pointer' }}"
                       class="{{ $isMyGoods?'':'btn-like' }} list-name like-shops" data-type="shops"
                       data-method="post" data-id="{{ $shop->id }}">
                        @if(is_null($isLike))
                            <i class="fa fa-star-o"></i> 收藏本店
                        @else
                            <i class="fa fa-star"></i> 已收藏
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <div class="row nav-wrap list-penal">
            <div class="col-sm-2 hot-goods-panel">
                <div class="col-sm-12 hot-goods ">
                    <a>店家热门商品</a>
                </div>
                @foreach($hotGoods as $good)
                    <div class="commodity commodity-border">
                        <div class="img-wrap">
                            <a href="{{ url('goods/' . $good->id) }}" target="_blank">
                                <img class="commodity-img lazy"
                                     data-original="{{ $good->image_url }}"/>
                                <span class="@if($good->is_out)prompt  lack  @elseif($good->is_promotion)prompt  promotions @elseif($good->is_new)prompt  new-listing @endif"></span>
                            </a>
                        </div>
                        <div class="content-panel">
                            <p class="commodity-name">
                                <a href="{{ url('goods/' . $good->id) }}"
                                   target="_blank">{{ $good->name }}</a>
                            </p>

                            <p class="sell-panel">
                                <span class="money red">¥{{ $good->price . '/' . $good->pieces }}</span>
                                <span class="sales pull-right">最低购买 : {{ $good->min_num }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-sm-10 pd-right-clear">
                <div class="col-sm-12 switching">
                    <a href="javascript:void(0)" id="graphic-details" class="active">图文详情</a>
                    <a href="javascript:void(0)" id="location">配送区域</a>
                </div>
                <div class="col-sm-12 graphic-details box active">
                    <div class="row">
                        <div class="col-sm-12 details">
                            @foreach($attrs as $key=>$attr)
                                <div class="item">{{ $key }} :{{ $attr }}</div>
                            @endforeach
                            <div class="item">包装:{{ $goods->pieces }}</div>
                        </div>
                        <div class="col-sm-12 padding-clear">
                            {!! $goods->introduce !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 address-wrap location box ">
                    <div class="row">
                        <div class="col-sm-5">
                            <table class="table margin-clear ">
                                <tr>
                                    <th>商品配送区域</th>
                                </tr>
                                @foreach($goods->deliveryArea as $area)
                                    <tr>
                                        <td>{{ $area->address_name }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>


                    {{--<div class="item">--}}
                    {{--<h5 class="title-name ">商品配送区域大概地图标识:</h5>--}}
                    {{--<p class="map ">--}}
                    {{--<img src="http://placehold.it/470x350">--}}
                    {{--</p>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('includes.cart')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            });
            $(".carousel-indicators li").mousemove(function () {
                var self = $(this);
                self.parents(".carousel").stop(true).carousel(self.index());
            });

            joinCart();
            numChange();
            tabBox();
            likeFunc();
        });
    </script>
@stop