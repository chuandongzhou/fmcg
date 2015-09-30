@extends('master')



@section('title')@yield('subtitle') | 快销平台@stop



@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop



@section('header')
    @include('index.index-top')
    <div class="container">
        <div class="row">
            <div class="col-sm-2 categories-btn">
                <a class="btn btn-primary">全部商品分类</a>
            </div>
            <div class="col-sm-10">
                <a href="{{ url('/') }}" class="btn">首页</a>
                <a href="{{ url('shop') }}" class="btn">商家</a>
            </div>
        </div>
    </div>
@stop

@section('body')
    <div class="banner-wrap">
        <div class="container dealer-index-banner">
            <div class="row categories-menu-item">
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
                                        <h3 class="title"><a
                                                    href="{{ url('search?category_id=2'. $child['id']) }}">{{ $child['name'] }}</a>
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
                                    <a href="{{ $advert['url'] }}"><img src="{{ $advert->image_url }}"
                                                                        alt="{{ $advert->name }}"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container dealer-index index">
        <div class="row list-penal">
            <div class="col-sm-12 title"><h3>热门商品</h3></div>
            @foreach($hotGoods as $hot)
                <a href="{{ url('goods/'.$hot->id) }}">
                    <div class="col-sm-3 commodity">
                        <div class="img-wrap">
                            <a href="{{ url('goods/' . $hot->id) }}"><img class="commodity-img"
                                                                          src="{{ $hot->image_url }}"></a>
                            <span class="prompt @if($hot->is_out) lack  @elseif($hot->is_promotion) promotions @elseif($hot->is_new) new-listing @endif"></span>
                        </div>
                        <div class="content-panel">
                            <p class="commodity-name"><a href="{{ url('goods/' . $hot->id) }}">{{ $hot->name }}</a></p>

                            <p class="sell-panel">
                                <span class="money">￥{{ $hot->price }}</span>
                                <span class="sales pull-right">销量 : {{ $hot->sales_volume }}</span>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="row list-penal">
            <div class="col-sm-12 title"><h3>热门经销商</h3></div>
            @foreach($hotShops as $shop)
                <a href="{{ url('shop/detail/'.$shop->id) }}">
                    <div class="col-sm-3 commodity">
                        <div class="img-wrap">
                            <a href="{{ url('shop/' . $shop->id) }}">
                                <img class="commodity-img" src="{{ $shop->image_url }}">
                            </a>
                        </div>
                        <div class="content-panel">
                            <p class="commodity-name"><a href="{{ url('shop/' . $shop->id) }}">{{ $shop->name }} </a>
                            </p>

                            <p class="sell-panel">
                                <span class="money">最低配送额 : ￥{{ $shop->min_money }}</span>
                                <span class="sales pull-right">销量 : 2000</span>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@stop



@section('footer')
    <footer class="panel-footer">
        <div class="container text-center text-muted">&copy;2003-2015 版权所有</div>
    </footer>
@stop

@section('js')
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 2000
            })
        });
    </script>
@stop