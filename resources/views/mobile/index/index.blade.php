@extends('mobile.master')

@section('subtitle', '首页')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item">
        <div class="row nav-top">
            <div class="col-xs-2 menu-sort">
                <a href="{{ url('category') }}">
                    <i class="iconfont icon-fenlei"></i>
                    <span>分类</span>
                </a>
            </div>
            <div class="col-xs-7 pd-clear search-item">
                <div class="panel">
                    <i class="iconfont icon-search"></i>
                    <input type="text" class="search" onclick="window.location.href='{{ url('search') }}'"
                           placeholder="查找商品"/>
                </div>
            </div>
            <div class="col-xs-3 pd-left-clear">
                <a id="txt_area" data-level="2"
                   data-id="{{ $addressData['province_id'] . ',' . $addressData['city_id'] }}">
                    {{ $addressData['city_name'] }}
                </a>
            </div>
        </div>
    </div>
@stop

@section('body')
    <div class="container-fluid  m60 p65">
        <div class="row ">
            <div class="col-xs-12">
                <div id="myCarousel" class="row carousel slide banner-slide index-slide">
                    <ol class="carousel-indicators">
                        @foreach($adverts as $key => $advert )
                            <li data-target="#myCarousel" data-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($adverts as $key => $advert )
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
        <div class="row">
            <div class="col-xs-12 menu-list-wrap">
                @foreach($categories as $key => $category)
                    <div class="list-item">
                        <i class="icon jiushui"></i>
                        <div class="item-name">
                            <a href="{{ url('category?cate=' . $category['id']) }}">{{ $category['name'] }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @foreach($goodsColumns as $index=>$column)
            @if(!$column->goods->isEmpty())
                <div class="row sort-wrap">
                    <div class="col-xs-12 text-center sort-item-title">
                        <i class="spot"></i>{{ $column->name  }}
                    </div>
                    <div class="col-xs-12 pd-clear product-wrap">
                        @foreach($column->goods as $goods)
                            @if ($goods->price > 0)
                                <div class="product-col">
                                    <a href="{{ url('goods/' . $goods->id) }}">
                                        <img class="product-img lazy" data-original="{{ $goods->image_url }}">
                                        <span class="@if($goods->is_out)prompt lack @elseif($goods->is_promotion)prompt promotions @elseif($goods->is_new)prompt new-listing @endif"></span>
                                        <div class="product-info">
                                            <div class="product-name">{{ $goods->name }}</div>
                                            <div class="product-price red">
                                                ¥{{ $goods->price . '/' . $goods->pieces }}</div>
                                        </div>
                                    </a>

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@stop

@include('mobile.includes.footer')

@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script type="text/javascript" src="{{ asset('mobile/dialog.js') }}"></script>
    <script type="text/javascript" src="{{ asset('mobile/mobile-select-area.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var json = formatAddress(addressData)
                , addressArea = $('#txt_area');
            addressSelect(json, '#txt_area', addressArea, function (scroller, text, value) {

                setCookie('province_id', value[0]);
                setCookie('city_id', value[1]);
                window.location.reload();
            }, true);

        });
        //定位
        if (!Cookies.get('province_id')) {
            setAddressCookie();
        }
    </script>
@stop