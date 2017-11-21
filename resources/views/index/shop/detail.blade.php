@extends('index.master')

@section('subtitle', '店家详情')
@include('includes.jquery-lazeload')
@include('includes.shop-advert-model')
@section('container')
    @include('includes.shop-search')
    <div class="container wholesalers-index index container-wrap">
        <div class="row">
            <div class="col-sm-12 left-store-logo padding-clear">
                <div id="myCarousel" class="carousel slide banner-slide index-slide">
                    <ol class="carousel-indicators">
                        @if(!$shop->adverts->isEmpty())
                            @for($index = 0; $index < $shop->adverts->count(); $index++)
                                <li data-target="#myCarousel" data-slide-to="{{ $index }}"
                                    class="{{ $index == 0 ? 'active' : '' }}">
                            @endfor
                        @endif
                    </ol>
                    <div class="carousel-inner banner">
                        @if(!$shop->adverts->isEmpty())
                            @foreach($shop->adverts as $key => $image)
                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                    <a href="{{ $image->url }}" target="_blank">
                                        <img src="{{ $image->image_url }}">
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="item active">
                                <img src="{{ asset('images/shop-banner.jpg') }}" alt="店铺图片">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row list-penal" id="rxp">
            <div class="col-sm-12 padding-clear classify-title">
                热销品
            </div>

            @include('includes.goods-list' , ['goods'=> $hotGoods])
        </div>
        <div class="row list-penal" id="dptj">
            <div class="col-sm-12 padding-clear classify-title">
                店铺推荐
            </div>

            @include('includes.goods-list' , ['goods'=> $recommendGoods])
        </div>
        <div class="row list-penal">
            <div class="col-sm-12 padding-clear classify-title">
                店铺地理位置
            </div>
            <div class="shop-address">
                <div data-group="shop" class="baidu-map" id="shop" data-address="{{ $shop->address }}"
                     data-lng="{{ $shop->x_lng }}"
                     data-lat="{{ $shop->y_lat }}">
                </div>
            </div>
        </div>
        <div class="row list-penal" id="psdq">
            <div class="col-sm-12 padding-clear classify-title">
                配送地区
            </div>
            <div class="col-sm-12 area-wrap">
                <table class="table-bordered table margin-clear table-center">

                    <tr>
                        <th>配送区域</th>
                        <th>配送额(元)</th>
                        <th>配送区域</th>
                        <th>配送额(元)</th>
                    </tr>
                    @for($i=0;$i<count($shop->deliveryArea);$i = $i+2 )
                        <tr>
                            <td>{{ $shop->deliveryArea[$i]->address_name }}</td>
                            <td>{{ $shop->deliveryArea[$i]->min_money }}</td>
                            @if($i+1 < count($shop->deliveryArea))
                                <td>{{ $shop->deliveryArea[$i+1]->address_name }}</td>
                                <td>{{ $shop->deliveryArea[$i+1]->min_money }}</td>
                            @endif
                        </tr>
                    @endfor

                </table>
            </div>
        </div>
        {{--<div class="row">--}}
        {{--<div class="col-xs-12 text-right">--}}
        {{--<ul class="pagination">--}}
        {{--<li class="disabled">--}}
        {{--<span>«</span>--}}
        {{--</li>--}}
        {{--<li class="active">--}}
        {{--<span>1</span>--}}
        {{--</li>--}}
        {{--<li>--}}
        {{--<a href="#">2</a>--}}
        {{--</li>--}}
        {{--<li>--}}
        {{--<a href="#">3</a>--}}
        {{--</li>--}}
        {{--<li>--}}
        {{--<a href="#">4</a>--}}
        {{--</li>--}}
        {{--<li class="disabled">--}}
        {{--<span>...</span>--}}
        {{--</li>--}}
        {{--<li>--}}
        {{--<a href="#" rel="next">»</a>--}}
        {{--</li>--}}
        {{--</ul>--}}
        {{--</div>--}}
        {{--</div>--}}
    </div>
    @include('includes.cart')
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            likeFunc();
            joinCart();
            numChange();
            var baiduMap = initMap();

            $('.carousel').carousel({
                interval: 2000
            });
            $('.advert-content').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            });
            {{--getCoordinateMap({!! $coordinates !!});--}}
        });
    </script>
@stop
