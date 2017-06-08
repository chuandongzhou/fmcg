@extends('mobile.master')

@section('subtitle', '商品列表')

@include('includes.jquery-lazeload')

@section('body')
    @parent
    <div class="fixed-header fixed-item white-bg search-nav">
        <div class="row nav-top white-bg">
            <div class="col-xs-2">
                <a class="iconfont icon-fanhui2 go-back" href="javascript:window.history.back()"></a>
            </div>
            @if($cateName)
                <div class="col-xs-10 color-black pd-left-clear">
                    {{ $cateName }}
                </div>
            @else
                <form action="{{ url('goods') }}" method="get">
                    <div class="col-xs-7 pd-clear search-item white-bg">
                        <input type="text" class="search" name="name" value="{{ $name }}" placeholder="查找商品"/>
                    </div>
                    <div class="col-xs-3 pd-clear">
                        <input type="submit" class="btn btn-search" value="搜索"/>
                    </div>
                </form>
            @endif
        </div>
    </div>
    <div class="container-fluid m60 ">
        @if($goods->count())
            <div class="row sort-wrap white-bg">
                <div class="col-xs-12 pd-clear product-wrap goods-list">
                    @foreach($goods as $item)
                        <div class="product-col">
                            <a href="{{ url('goods/' . $item->goods) }}">
                                <img class="product-img lazy" data-original="{{ $item->image_url }}">
                                <span class="@if($item->is_out)prompt lack @elseif($item->is_promotion)prompt promotions @elseif($item->is_new)prompt new-listing @endif"></span>
                                <div class="product-info">
                                    <div class="product-name">{{ $item->name }}</div>
                                    <div class="product-price red">¥{{ $item->price . '/' . $item->pieces }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                        <div class="col-xs-12 text-center  loading-image hidden">
                            <i class="fa fa-spinner fa-pulse"></i>
                        </div>
                </div>

            </div>
        @else
            <div class="row">
                <div class="col-xs-12">
                    <i class="iconfont icon-tishi search-prompt"></i>没有找到相关商品
                </div>
            </div>
        @endif
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        //滑到最底部时加载商品
        var loadingImage = $('.loading-image'), page = 1;
        function loadGoods() {
            if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                var url = window.location.href, urls = url.split('?'), queryString = urls[1];
                queryString = queryString === undefined ? '' : queryString.replace(/[\&\?]*page=\d+/, '');
                loadingImage.removeClass('hidden');
                document.removeEventListener('touchmove', loadGoods, false);
                var pivot = queryString ? '&' : '';
                queryString = queryString + pivot + 'page=' + (page + 1);
                $.ajax({
                    url: site.url('goods'),
                    method: 'get',
                    data: queryString
                }).done(
                    function (data) {
                        var goods = data.goods, goodsHtml = '';
                        for (var i in goods) {
                            var item = goods[i];
                            goodsHtml += '<div class="product-col">' +
                                '   <a href="' + site.url('goods/' + item.id) + '">' +
                                '   <img class="product-img lazy" src="' + item.image_url + '">' +
                                '   <span class=""></span>' +
                                '   <div class="product-info">' +
                                '   <div class="product-name">' + item.name + '</div>' +
                                '   <div class="product-price red">¥' + item.price + '/' + item.pieces + '</div>' +
                                '   </div>' +
                                '   </a>' +
                                '   </div>';
                        }
                        loadingImage.before(goodsHtml);
                        if (goods.length) {
                            page = page + 1
                            loadingImage.addClass('hidden');
                            document.addEventListener('touchmove', loadGoods, false);
                        } else {

                            loadingImage.html('没有更多数据')
                        }
                    }
                ).fail(function () {
                    loadingImage.addClass('hidden');
                    document.addEventListener('touchmove', loadGoods, false);
                    showMassage('服务器错误');
                });

            }
        }
        document.addEventListener('touchmove', loadGoods, false);

    </script>
@stop