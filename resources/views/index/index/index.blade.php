@extends('index.master')

@section('subtitle', '首页')

@include('includes.jquery-lazeload')

@section('header')
    @parent
    @include('index.search')
    <div class="container categories-wrap" id="categories-wrap">
        <div class="row">
            <div class="col-xs-2 categories-btn">
                <a>全部商品分类</a>
            </div>
            <div class="col-xs-10 nav-name ">
                <a href="{{ url('/') }}">首页</a>
                @if((isset($user) && $user->type == cons('user.type.retailer')) || is_null($user))
                    <a href="{{ url('shop?type=wholesaler') }}">批发商</a>
                @endif
                <a href="{{ url('shop?type=supplier') }}">供应商</a>
            </div>
        </div>
        <div class="row categories-menu-item">
            <div class="col-xs-2 categories padding-clear">
                <ul class="menu-wrap">
                    @foreach($categories as $category)
                        <li><a class="one-title"
                               href="{{ url('search?category_id=1'. $category['id']) }}"><i
                                        class="iconfont icon-{{ pinyin($category['name'])[0].pinyin($category['name'])[1] }} "></i> {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-xs-8 menu-down-wrap">
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
            </div>
        </div>
    </div>
@stop

@section('container')
    <div class="container-wrap">
        <div class="container-fluid  dealer-index-banner padding-clear">
            <div class="row  margin-clear">
                <div class="col-xs-12">
                    <div id="myCarousel" class="row carousel slide banner-slide index-slide">
                        <ol class="carousel-indicators">
                            @foreach($adverts as $key=>$advert )
                                <li class="{{ $key == 0 ? 'active' : '' }}" data-target="#myCarousel"
                                    data-slide-to="{{ $key }}">

                                </li>
                            @endforeach

                        </ol>
                        <div class="carousel-inner">
                            @foreach($adverts as $key=>$advert )
                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                    <a href="{{ $advert->url }}" target="_blank">
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
        @foreach($goodsColumns as $index=>$column)
            @if(!$column->goods->isEmpty())
                <div class="row list-penal">
                    <div class="col-xs-12 title padding-clear">
                        <div class="category-name"><span
                                    class="text-center first-letter">{{ strtoupper(pinyin_abbr($column->name)[0]) }}</span>{{ $column->name  }}
                        </div>
                    </div>
                    <div class="col-xs-12 padding-clear">

                        <div class="padding-clear left-menu-wrap pull-left">

                            <a href="{{  !$column->leftAdverts->isEmpty()?$column->leftAdverts[0]->url:'' }}"><img
                                        class="commodity-img lazy"
                                        data-original="{{ !$column->leftAdverts->isEmpty()?$column->leftAdverts[0]->image_url:asset('images/advert/left-category.jpg') }}"></a>

                            <ul class="secondary-menu">
                                @foreach($categories[$column->id]['child'] as $child)
                                    <li>
                                        <a href="{{ url('search?category_id=' . $child['level'].$child['id']) }}">{{ $child['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="pull-left center-wrap commodity-panel commodity-index-panel padding-clear">

                            @foreach($column->goods as $goods)
                                @if ($goods->price > 0)
                                    <div class="commodity commodity-index-img commodity-border ">
                                        <div class="img-wrap">
                                            <a href="{{ url('goods/' . $goods->id) }}" target="_blank">
                                                <img class="commodity-img lazy"
                                                     data-original="{{ $goods->image_url }}"/>
                                                <span class="@if($goods->is_out)prompt lack @elseif($goods->is_promotion)prompt promotions @elseif($goods->is_new)prompt new-listing @endif"></span>
                                            </a>
                                        </div>
                                        <div class="content-panel">
                                            <a href="{{ url('goods/' . $goods->id) }}" target="_blank">
                                                <p class="commodity-name">{{ $goods->name }}</p>

                                                        <p class="sell-panel">
                                                            <span class="money red">¥{{ $goods->price . '/' . $goods->pieces }}</span>
                                                        </p>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                        </div>
                        <div class=" pd-left-clear right-wrap pull-left">
                            <div id="myCarousel{{ $index }}" class=" carousel slide banner-slide">
                                @if(!$column->adverts->isEmpty())
                                    <div class="carousel-inner">
                                        @foreach($column->adverts as $key => $advert)
                                            <div class="item {{ $key == 0 ? 'active' : '' }}">
                                                <a href="{{ $advert->url }}" target="_blank">
                                                    <img src="{{ $advert->image_url }}"
                                                         alt="{{ $advert->name }}">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <ul class="carousel-indicators carousel-indicators-item">
                                        @foreach($column->adverts as $key=>$image)
                                            <li class="{{ $key == 0 ? 'active' : '' }}"
                                                data-target="#myCarousel{{ $index }}"
                                                data-slide-to="{{ $key }}">

                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="carousel-inner">
                                        <div class="item active">
                                            <a href="#">
                                                <img src="{{ asset('images/advert/category.jpg') }}"
                                                     alt="{{ asset('images/advert/category.jpg') }}">
                                            </a>
                                        </div>
                                    </div>
                                @endif

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
        <div class="container join-title text-center padding-clear">
            <a href="javascript:" data-toggle="modal"
               data-target="#myModal-agreement"><img src="{{ asset('images/bottom.jpg') }}"/></a>
        </div>
        @include('includes.agreement')
    @endif
@stop