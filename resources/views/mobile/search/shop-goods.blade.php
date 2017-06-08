@extends('mobile.master')

@section('subtitle', '搜索')

@section('body')
    @parent
    <div class="fixed-header fixed-item white-bg search-nav">
        <div class="row nav-top white-bg">
            <form action="{{ url("shop/{$shop->id}/goods") }}" method="get">
                <div class="col-xs-2">
                    <a class="iconfont icon-fanhui2 go-back" href="javascript:window.history.back()"></a>
                </div>
                <div class="col-xs-7 pd-clear search-item white-bg">
                    <input type="text" name="name" class="search" placeholder="查找商品"/>
                </div>
                <div class="col-xs-3 pd-clear">
                    <input type="submit" class="btn btn-search" value="搜索"/>
                </div>
            </form>
        </div>
    </div>
    <div class="container-fluid m60">
        <div class="row">
            <div class="col-xs-12 search-page">
                <div class="title">搜索记录：</div>
                <div class="search-annal">
                    @foreach($keywords as $goodsName=>$count)
                        <span><a href="{{ url("shop/{$shop->id}/goods?name=" . $goodsName) }}">{{ $goodsName }}</a></span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop