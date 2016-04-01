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
            @include('includes.goods-list' , ['goods'=> $goods])
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                {!! $goods->render() !!}
            </div>
        </div>
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
            $('.carousel').carousel({
                interval: 2000
            })
        });
    </script>
@stop