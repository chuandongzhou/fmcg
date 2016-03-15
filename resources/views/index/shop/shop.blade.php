@extends('index.master')

@section('subtitle', '店家商品')

@section('container')
    @include('index.shop-search')
    <div class="container wholesalers-index index contents">
        <div class="row">
            <div class="col-sm-12 left-store-logo">
                <div id="myCarousel" class="carousel slide banner banner-slide">
                    <ol class="carousel-indicators">
                        @for($index = 0; $index < count($shop->images); $index++)
                            <li data-target="#myCarousel" data-slide-to="{{ $index }}"
                                class="{{ $index == 0 ? 'active' : '' }}">
                        @endfor
                    </ol>
                    <div class="carousel-inner banner">
                        @foreach($shop->images as $key=>$image)
                            <div class="item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ $image->url }}" alt="{{ $image->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{--<div class="col-sm-4 store">--}}
            {{--<div class="store-panel">--}}
            {{--<img class="avatar" src="{{ $shop->logo_url }}">--}}
            {{--<ul class="store-msg">--}}
            {{--<li>店家姓名:{{ $shop->user->nickname }}</li>--}}
            {{--<li>联系人:{{ $shop->contact_person }}</li>--}}
            {{--<li>最低配送额:￥{{ $shop->min_money }}</li>--}}
            {{--</ul>--}}
            {{--</div>--}}
            {{--<div class="address-panel">--}}
            {{--<ul>--}}
            {{--<i class="icon icon-tel"></i>--}}
            {{--<li class="address-panel-item">--}}
            {{--<span class="panel-name">联系方式</span>--}}
            {{--<span>{{ $shop->contact_info }}</span>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--<ul>--}}
            {{--<i class="icon icon-seller"></i>--}}
            {{--<li class="address-panel-item">--}}
            {{--<span class="panel-name">店家地址</span>--}}
            {{--<span>{{ $shop->address }}</span>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--<ul>--}}
            {{--<i class="icon icon-address"></i>--}}
            {{--<li class="address-panel-item">--}}
            {{--<span class="panel-name">商品配送区域</span>--}}

            {{--<div class="address-list">--}}
            {{--@foreach ($shop->deliveryArea as $area)--}}
            {{--<span>{{ $area->address_name }}</span>--}}
            {{--@endforeach--}}
            {{--</div>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
        <div class="row">
            <div class="col-sm-12 ">
                <div class="tab-title clearfix">
                    <p class="sequence">
                        <a class="{{ $sort == 'all' || !$sort  ? 'active' : '' }}"
                           href="{{ url('shop/' . $shop->id) }}">全部</a>

                        @foreach(cons('sort.goods') as $key=>$sortName)
                            <a class="{{ $sort == $key ? 'active' : '' }}"
                               href="{{ url('shop/'.$shop->id . '/'.$key) }}">
                                {{ cons()->valueLang('sort.goods' , $sortName) }}
                            </a>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            @foreach($goods as $item)
                @if ($item->price > 0)
                    <div class="col-sm-3 commodity">
                        <div class="img-wrap">
                            <a href="{{ url($url . '/' . $item->id) }}" {{ $shop->user->id == auth()->id() ? '' : 'target="_blank"' }} >
                                <img class="commodity-img" src="{{  $item->image_url }}">
                                <span class="@if($item->is_out) prompt lack  @elseif($item->is_promotion) prompt promotions @elseif($item->is_new) prompt new-listing @endif"></span>
                            </a>
                        </div>
                        <div class="content-panel">
                            <p class="commodity-name"><a href="{{ url($url . '/' . $item->id) }}">{{ $item->name }}</a>
                            </p>

                            <p class="sell-panel">
                                <span class="money">￥{{ $item->price }}</span>
                                <span class="sales pull-right">销量 : {{ $item->sales_volume }}</span>
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
            <div class="col-xs-12 text-right">
                {!! $goods->render() !!}
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            likeFunc();
            $('.carousel').carousel({
                interval: 2000
            })
        });
    </script>
@stop