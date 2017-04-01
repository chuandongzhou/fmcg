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
            @foreach($hotGoods as $hotGoodsDetail)
                <div class="col-sm-3 commodity new-listing commodity-search-product">
                    <div class="commodity-border">
                        <div class="img-wrap">
                            <a href="{{ url('goods/'.$hotGoodsDetail->id) }}" target="_blank">
                                <img class="commodity-img lazy" data-original="{{ $hotGoodsDetail->image_url }}">
                                <span class="@if($hotGoodsDetail->is_out)prompt  lack  @elseif($hotGoodsDetail->is_promotion)prompt  promotions @elseif($hotGoodsDetail->is_new)prompt  new-listing @endif"></span>
                            </a>
                        </div>
                        <div class="content-panel">
                            <a href="{{ url('goods/'.$hotGoodsDetail->id) }}" target="_blank">
                                <div class="commodity-name">{{ $hotGoodsDetail->name }}</div>
                            </a>
                            <div class="sell-panel">
                                <span class="money red">¥{{ $hotGoodsDetail->price . '/' . $hotGoodsDetail->pieces }}</span>
                                <span class="sales pull-right">最低购买量 : {{ $hotGoodsDetail->min_num }}</span>
                            </div>
                            {{--<div class="store-name prompt">{{ $hotGoodsDetail->shop->name }}</div>--}}
                            <div class="shopping-store">
                                <button type="button" data-group="group{{ $hotGoodsDetail->id }}"
                                        class="count modified desc-num"
                                        disabled>-
                                </button>
                                <input type="text" data-group="group{{ $hotGoodsDetail->id }}" class="amount num"
                                       name="num"
                                       value="{{ $hotGoodsDetail->min_num }}"
                                       data-min-num="{{ $hotGoodsDetail->min_num }}">
                                <button type="button" data-group="group{{ $hotGoodsDetail->id }}"
                                        class="count modified inc-num">+
                                </button>
                                @if($hotGoodsDetail->is_out)
                                    <a href="javascript:void(0)" class="btn btn-primary disabled join-cart" disabled="">缺货</a>
                                @else

                                    <a href="javascript:void(0)"
                                       data-url="{{ $user->id==$shop->user_id?'':url('api/v1/cart/add/'.$hotGoodsDetail->id) }}"
                                       class="btn btn-primary join-cart {{ $user->id==$shop->user_id?'disabled':'' }}"
                                       data-group="group{{ $hotGoodsDetail->id }} ">加入购物车</a>


                                @endif
                                <div class="sales prompt">累积销量：{{ $hotGoodsDetail->sales_volume }}</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
        <div class="row list-penal" id="dptj">
            <div class="col-sm-12 padding-clear classify-title">
                店铺推荐
            </div>
            @foreach($recommendGoods as $recommendGoodsDetail)
                <div class="col-sm-3 commodity new-listing commodity-search-product">
                    <div class="commodity-border">
                        <div class="img-wrap">
                            <a href="{{ url('goods/'.$recommendGoodsDetail->id) }}" target="_blank">
                                <img class="commodity-img lazy" data-original="{{ $recommendGoodsDetail->image_url }}">
                                <span class="@if($recommendGoodsDetail->is_out)prompt  lack  @elseif($recommendGoodsDetail->is_promotion)prompt  promotions @elseif($recommendGoodsDetail->is_new)prompt  new-listing @endif"></span>
                            </a>
                        </div>

                        <div class="content-panel">
                            <a href="{{ url('goods/'.$recommendGoodsDetail->id) }}" target="_blank">
                                <div class="commodity-name">{{ $recommendGoodsDetail->name }}</div>
                            </a>
                            <div class="sell-panel">
                                <span class="money red">¥{{ $recommendGoodsDetail->price . '/' . $recommendGoodsDetail->pieces }}</span>
                                <span class="sales pull-right">最低购买量 : {{ $recommendGoodsDetail->min_num }}</span>
                            </div>
                            {{--<div class="store-name prompt">{{ $recommendGoodsDetail->shop->name }}</div>--}}
                            <div class="shopping-store">
                                <button type="button" data-group="group{{ $recommendGoodsDetail->id }}"
                                        class="count modified desc-num"
                                        disabled>-
                                </button>
                                <input type="text" data-group="group{{ $recommendGoodsDetail->id }}" class="amount num"
                                       name="num"
                                       value="{{ $recommendGoodsDetail->min_num }}"
                                       data-min-num="{{ $recommendGoodsDetail->min_num }}">
                                <button type="button" data-group="group{{ $recommendGoodsDetail->id }}"
                                        class="count modified inc-num">+
                                </button>
                                @if($recommendGoodsDetail->is_out)
                                    <a href="javascript:void(0)" class="btn btn-primary disabled join-cart" disabled="">缺货</a>
                                @else
                                    @if($user->id==$shop->user_id)
                                        <a href="javascript:void(0)" disabled="disabled"
                                           class="btn btn-primary disabled join-cart" disabled="">加入购物车</a>
                                    @else

                                        <a href="javascript:void(0)"
                                           data-url="{{$user->id==$shop->user_id?'': url('api/v1/cart/add/'.$recommendGoodsDetail->id) }}"
                                           class="btn btn-primary join-cart {{ $user->id==$shop->user_id?'disabled':'' }}"
                                           data-group="group{{ $recommendGoodsDetail->id }}">加入购物车</a>
                                    @endif

                                @endif
                                <div class="sales prompt">累积销量：{{ $recommendGoodsDetail->sales_volume }}</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
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
