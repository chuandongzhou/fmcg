@extends('index.master')

@section('subtitle', '店家商品')

@include('includes.jquery-lazeload')
@include('includes.shop-advert-model')
@section('container')
    @include('index.shop-search')
    <div class="container wholesalers-index index contents">
        <div class="row">
            <div class="col-sm-12 left-store-logo">
                <div id="myCarousel" class="carousel slide banner banner-slide">
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
                                    @if($image->type==5)
                                    <a href="{{ $image->url }}">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->name }}">
                                    </a>
                                     @else
                                        <a class="advert-content" data-toggle="modal" data-target="#shopAdvertModal" data-content="{{ $image->url }}" title="{{ $image->name }}">
                                            <img src="{{ $image->image_url }}" alt="{{ $image->name }}">
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="item active">
                                <img src="{{ asset('images/default-shop-image.jpg') }}" alt="店铺图片">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 ">
                <div class="tab-title clearfix">
                    <p class="sequence">
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
            $('.advert-content').on('click', function () {
                var obj = $(this);
                $('.modal-title').html(obj.attr('title'));
                $('.notice-content').html(obj.data('content'));
            });
        });
    </script>
@stop