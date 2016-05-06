@extends('index.master')

@section('subtitle', '首页')

@include('includes.notice')
@include('includes.jquery-lazeload')

@section('header')
    @parent
    @include('index.search')
    <div class="container">
        <div class="row margin-clear">
            <div class="col-sm-2 categories-btn">
                <a class="btn btn-primary">全部商品分类</a>
            </div>
            <div class="col-sm-10 nav-name">
                {{--<a href="{{ url('/') }}" class="btn">首页</a>--}}
                @if((isset($user) && $user->type == cons('user.type.retailer')) || is_null($user))
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
                <div class="col-sm-8 menu-down-wrap">
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-sm-12 menu-down-layer menu-down-item">
                                @if(isset($category['child']))
                                    @foreach($category['child'] as $child)
                                        <div class="item">
                                            <h3 class="title">
                                                <a href="{{ url('search?category_id=2'. $child['id']) }}">{{ $child['name'] }}</a>
                                            </h3>
                                            @if(isset($child['child']))
                                                @foreach($child['child'] as $grandChild)
                                                    <a href="{{ url('search?category_id=3'. $grandChild['id']) }}">{{ $grandChild['name'] }}</a>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div id="myCarousel" class="carousel row slide banner-slide index-slide">
                        <ol class="carousel-indicators">
                            @foreach($adverts as $key=>$advert )
                                <li class="{{ $key == 0 ? 'active' : '' }}" data-target="#myCarousel"
                                    data-slide-to="{{ $key }}">
                                    {{ $key + 1 }}
                                </li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($adverts as $key=>$advert )
                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                    <a href="{{ $advert->url }}" target="_blank">
                                        <img class="lazy" data-original="{{ $advert->image_url }}"
                                             alt="{{ $advert->name }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 upcoming-events">
                    <h3 class="text-center">活动公告</h3>

                    @foreach($notices as $key=>$notice)
                        <p>
                            <a class="content-title" href="javascript:" data-target="#noticeModal" data-toggle="modal"
                               data-content="{{ $notice->content }}"
                               title="{{ $notice->title }}">{{ ($key+1). '.' .$notice->title }}
                            </a>
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="container dealer-index index contents">
        @foreach($goodsColumns as $index=>$column)
            @if(!$column->goods->isEmpty())
                <div class="row list-penal">
                    <div class="col-xs-12 title"><h3>{{ $column->name }} <a
                                    href="{{ url('search?category_id=' . $column->level.$column->id) }}">进入>></a></h3>
                    </div>
                    <div class="col-xs-12 padding-clear">
                        <div class="row margin-clear">
                            <div class="col-xs-5">
                                <div id="myCarousel{{ $index }}" class="row carousel slide banner-slide">
                                    @if(!$column->adverts->isEmpty())
                                        <div class="carousel-inner">
                                            @foreach($column->adverts as $key => $advert)
                                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                                    <a href="{{ $advert->url }}" target="_blank">
                                                        <img class="lazy" data-original="{{ $advert->image_url }}"
                                                             alt="{{ $advert->name }}">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <ul class="carousel-indicators carousel-indicators-item">
                                            @foreach($column->adverts as $key=>$image)
                                                <li data-target="#myCarousel{{ $index }}" data-slide-to="{{ $key }}"
                                                    class="{{ $key == 0 ? 'active' : '' }}">{{ $image->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xs-7">
                                <div class="col-xs-12 commodity-panel padding-clear">
                                    @foreach($column->goods as $goods)
                                        @if ($goods->price > 0)
                                            <div class="commodity commodity-index-img">
                                                <div class="img-wrap">
                                                    <a href="{{ url('goods/' . $goods->id) }}" target="_blank">
                                                        <img class="commodity-img lazy"
                                                             data-original="{{ $goods->image_url }}"/>

                                                        <span class="@if($goods->is_out)prompt lack @elseif($goods->is_promotion)prompt promotions @elseif($goods->is_new)prompt new-listing @endif"></span>
                                                    </a>
                                                </div>
                                                <div class="content-panel">
                                                    <p class="commodity-name">
                                                        <a href="{{ url('goods/' . $goods->id) }}"
                                                           target="_blank">{{ $goods->name }}</a></p>

                                                    <p class="sell-panel">
                                                        <span class="money">￥{{ $goods->price . '/' . $goods->pieces }}</span>
                                                        <span class="sales pull-right">最低购买 : {{ $goods->min_num }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@stop

@section('join-us')
    @if(!isset($user))
        <div class="container join-title text-center">
            <a href="javascript:" data-toggle="modal"
               data-target="#myModal-agreement">加入订百达</a>
        </div>
        @include('includes.agreement')
    @endif
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $('.carousel').carousel({
                interval: 5000
            });
            $(".carousel-indicators li").mousemove(function () {
                var self = $(this);
                self.parents(".carousel").stop(true).carousel(self.index());
            });
            $(".carousel-indicators-item").each(function (e) {
                var obj = $(this), width = 100 / obj.children("li").length;
                obj.children("li").css("width", width + "%");
            });
            $('.content-title').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            })
        });
    </script>
@stop