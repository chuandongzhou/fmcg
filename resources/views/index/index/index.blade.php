@extends('index.master')

@section('subtitle', '首页')

@section('header')
    @parent
    @include('index.search')
    <div class="container">
        <div class="row margin-clear">
            <div class="col-sm-2 categories-btn">
                <a class="btn btn-primary">全部商品分类</a>
            </div>
            <div class="col-sm-10">
                {{--<a href="{{ url('/') }}" class="btn">首页</a>--}}
                @if($user->type == cons('user.type.retailer'))
                    <a href="{{ url('shop?type=wholesaler') }}" class="btn">批发商</a>
                @endif
                <a href="{{ url('shop?type=supplier') }}" class="btn">供应商</a>
            </div>
        </div>
    </div>
@stop

@section('container')
    <div class="banner-wrap">
        <div class="container dealer-index-banner">
            <div class="row categories-menu-item margin-clear">
                <div class="col-sm-2 categories">
                    <ul class="menu-wrap">
                        @foreach($categories as $category)
                            <li><a class="one-title"
                                   href="{{ url('search?category_id=1'. $category['id']) }}">{{ $category['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-10 menu-down-wrap">
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-sm-12 menu-down-layer menu-down-item">
                                @foreach($category['child'] as $child)
                                    <div class="item">
                                        <h3 class="title">
                                            <a href="{{ url('search?category_id=2'. $child['id']) }}">{{ $child['name'] }}</a>
                                        </h3>
                                        @foreach($child['child'] as $grandChild)
                                            <a href="{{ url('search?category_id=3'. $grandChild['id']) }}">{{ $grandChild['name'] }}</a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div id="myCarousel" class="carousel row slide banner-slide">
                        <ol class="carousel-indicators">
                            @foreach($adverts as $key=>$advert )
                                <li class="{{ $key == 0 ? 'active' : '' }}" data-target="#myCarousel"
                                    data-slide-to="{{ $key }}">
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($adverts as $key=>$advert )
                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                    <a href="{{ $advert['url'] }}" target="_blank">
                                        <img src="{{ $advert->image_url }}" alt="{{ $advert->name }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container dealer-index index contents">
        @foreach($goodsColumns as $column)
            @if(!$column->goods->isEmpty())
                <div class="row list-penal">
                    <div class="col-sm-12 title"><h3>{{ $column->name }}</h3></div>
                    <div class="col-sm-12 padding-clear">
                        @foreach($column->goods as $goods)
                            <div class="commodity commodity-index-img">
                                <div class="img-wrap">
                                    <a href="{{ url('goods/' . $goods->id) }}" target="_blank">
                                        <img class="commodity-img" src="{{ $goods->image_url }}" 、>

                                        <span class="@if($goods->is_out)prompt lack @elseif($goods->is_promotion)prompt promotions @elseif($goods->is_new)prompt new-listing @endif"></span>
                                    </a>
                                </div>
                                <div class="content-panel">
                                    <p class="commodity-name">
                                        <a href="{{ url('goods/' . $goods->id) }}"
                                           target="_blank">{{ $goods->name }}</a></p>

                                    <p class="sell-panel">
                                        <span class="money">￥{{ $goods->price }}</span>
                                        <span class="sales pull-right">最低购买量 : {{ $goods->min_num }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        @foreach($shopColumns as $column)
            @if(!$column->shops->isEmpty())
                <div class="row list-penal dealer-commodity-wrap">
                    <div class="col-sm-12 title"><h3>{{ $column->name }}</h3></div>
                    <div class="col-sm-12 padding-clear">
                        @foreach($column->shops as $shop)
                            <div class="commodity commodity-index-img">
                                <div class="img-wrap">
                                    <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                        <img class="commodity-img" src="{{ $shop->image_url }}">
                                    </a>
                                </div>
                                <div class="content-panel">
                                    <p class="commodity-name">
                                        <a href="{{ url('shop/' . $shop->id) }}"
                                           target="_blank">{{ $shop->name. ' (' . cons()->valueLang('user.type',  $shop->user->type) . ')' }} </a>
                                    </p>

                                    <p class="sell-panel">
                                        <span class="money">最低配送额 : ￥{{ $shop->min_money }}</span>
                                        {{--<span class="sales pull-right">销量 : {{ $shop->sales_volume }}</span>--}}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@stop


@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            })
        });
    </script>
@stop