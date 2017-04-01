@extends('index.master')

@section('subtitle', '首页')

@section('css')
    @parent
    <style>
         body {
            margin-bottom: 55px;
        }
        .footer{
            border: none;
        }
    </style>
@stop
@section('header')
    @parent
    @include('includes.search')
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
    @if(preg_match('/goods\/\d+/' , request()->path()))
        <div class="container-wrap text-center not-exist">
            <img src="{{ asset('images/not-exist.png') }}"/>
            <div>
                <p>商品不存在</p>
                <a href="{{ url('/') }}" class="btn btn-primary back-index">返回首页</a>
            </div>
        </div>
    @else
        <div class="container error-wrap">
            <img src="{{ asset('images/404.gif') }}">
            <a href="{{ url('/') }}" class="btn btn-primary back-index">返回首页</a>
        </div>
    @endif
@stop
