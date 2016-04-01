@extends('index.index-master')

@section('subtitle')
    {{ $goods->name }}
@stop
@section('container')
    <div class="container wholesalers-index goods-detail">
        <div class="row">
            <div class="col-sm-5 goods-detail-banner">
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
            <div class="col-sm-7 store-detail-wrap">
                <div class="store-title">
                    <h3 class="store-name">{{ $goods->name }}</h3>

                    <p class="title-content prompt">
                        @foreach($categoriesName as $cate)
                            <a href="{{ url('search?category_id=' . $cate['level'] . $cate['id']) }}" target="_blank">
                                {{ $cate['name'] }}
                            </a>
                            {{ $cate == $categoriesName->last() ? '' : ' -> ' }}
                        @endforeach
                    </p>
                </div>

                <div class="clearfix store-detail">
                    <ul class="pull-left left-panel">
                        <li><span class="prompt">商品ID :</span> <b>{{ $goods->id }}</b></li>
                        <li><span class="prompt">价格 :</span> <b>￥{{ $goods->price . ' / ' . $goods->pieces }}</b></li>
                        <li><span class="prompt">保质期 :</span> <b>{{ $goods->shelf_life}}</b></li>
                        @foreach($attrs as $key=>$attr)
                            <li>
                                <span class="prompt">{{ $key }} :</span> <b>{{ $attr }}</b>
                            </li>
                        @endforeach

                        <li><span class="prompt">累计销售量 :</span> <b>{{ $goods->sales_volume }}</b></li>
                    </ul>
                    <ul class="pull-left right-panel">
                        <li>
                            <span class="prompt">商家 :</span>
                            <b>
                                <a href="{{ url('shop/' . $goods->shop->id) }}" target="_blank">
                                    {{ $goods->shop->name }}
                                </a>
                                <a href="javascript:"
                                   onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$goods->shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                   class="contact"><span class="fa fa-commenting-o"></span> 联系客服</a>
                            </b>
                        </li>
                        <li><span class="prompt">条形码 :</span> <b>{{ $goods->bar_code }}</b></li>
                        @if($goods->is_promotion)
                            <li class="clearfix"><span class="prompt pull-left">促销信息 : </span>

                                <p class="promotions-content">{{ $goods->promotion_info }}</p></li>
                        @endif

                        <li>
                            <span class="prompt">退换货 :</span>
                            <b>{{ $goods->is_back ? '可退货' : '' }}  {{  $goods->is_change ? '可换货' : ($goods->is_back ? '' : '不可退 不可换')  }}</b>
                        </li>
                        <li>
                            <span class="prompt">即期品 :</span>
                            <b>{{ cons()->valueLang('goods.type' ,$goods->is_expire ) }}</b>
                        </li>
                        <li><span class="prompt">规格 :</span> <b>{{ $goods->specification or '暂无' }}</b></li>
                        <li>
                            <button disabled class="btn count btn-cancel desc-num">-</button>
                            <input type="text" class="amount num" name="num" value="{{ $goods->min_num }}"
                                   data-min-num="{{ $goods->min_num }}">
                            <button class="btn count btn-cancel inc-num">+</button>
                            <span class="prompt"> 最低购买量 :</span> {{ $goods->min_num }}
                        </li>
                        <li>
                            @if($goods->is_out)
                                <a href="javascript:void(0)" class="btn btn-primary disabled" disabled="">缺货</a>
                            @else
                                <a href="javascript:void(0)" data-url="{{ url('api/v1/cart/add/'.$goods->id) }}"
                                   class="btn btn-primary join-cart">加入购物车</a>
                            @endif
                            <a href="javascript:void(0)" data-type="goods" data-method="post"
                               class="btn btn-default btn-like" data-id="{{ $goods->id }}">
                                @if(is_null($isLike))
                                    <i class="fa fa-star-o"></i> 加入收藏夹
                                @else
                                    <i class="fa fa-star"></i> 已收藏
                                @endif
                            </a>
                        </li>
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
                    <h5 class="prompt">商品配送区域 :</h5>
                    <ul class="address-list">
                        @foreach($goods->deliveryArea as $area)
                            <p class="col-sm-12">{{ $area->address_name }}</p>
                        @endforeach
                    </ul>
                </div>
                {{--<div class="item">--}}
                {{--<h5 class="prompt">商品配送区域大概地图标识 :</h5>--}}

                {{--<div id="map"></div>--}}
                {{--</div>--}}
            </div>
            <div class="col-sm-12 box graphic-details">
                {!! $goods->introduce !!}
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
            {{--getCoordinateMap(--}}{{--{!! $coordinates !!}--}}{{--);--}}
        });
    </script>
@stop