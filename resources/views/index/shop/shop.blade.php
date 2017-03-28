@extends('index.master')

@section('subtitle', '店家商品')

@include('includes.jquery-lazeload')
@include('includes.shop-advert-model')
@section('container')
    @include('index.shop-search')
    <div class="container wholesalers-index index contents">
        <div class="row">
            <div class="col-sm-12  padding-clear">
                <div class="tab-title clearfix">
                    <p class="sequence">
                        @foreach(cons('sort.goods') as $key=>$sortName)
                            <a class="{{ $sort == $key ? 'active' : '' }}"
                               href="{{ url('shop/all-goods/'.$shop->id . '/'.$key) }}">
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