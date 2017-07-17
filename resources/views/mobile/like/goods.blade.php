@extends('mobile.master')

@section('subtitle', '我的收藏')

@include('includes.jquery-lazeload');

@section('header')
    <div class="fixed-header fixed-item">
        <div class="row nav-top white-bg orders-details-header">
            <div class="col-xs-12 color-black">我的收藏</div>
        </div>
        <div class="row">
            <div class="col-xs-12 collection-tab-menu">
                <a href="{{ url('like/shops') }}" class="shop">商铺</a>
                <a class="commodity active">商品</a>
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m90">
        <div class="row collection-commodity-container">
            @foreach($goods as $item)
                <a href="{{ url('goods/' . $item->id) }}">
                    <div class="col-xs-12 clearfix shop-list-item">
                        <img data-original="{{ $item->image_url }}" class="pull-left lazy"/>
                        <div class="commodity-name">
                            {{ $item->name }}
                        </div>
                    </div>
                </a>
            @endforeach
                <div class="col-xs-12 text-center  loading-image hidden">
                    <i class="fa fa-spinner fa-pulse"></i>
                </div>
        </div>
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
                    url: urls[0],
                    method: 'get',
                    data: queryString
                }).done(
                    function (data) {
                        var goods = data.goods, goodsHtml = '';
                        for (var i in goods) {
                            var item = goods[i];
                            goodsHtml += '<a href="' + site.url('goods/' + item.id) + '">' +
                                '    <div class="col-xs-12 clearfix shop-list-item">' +
                                '       <img src="' + item.image_url + '" class="pull-left"/>' +
                                '       <div class="commodity-name">' +
                                item.name +
                                '       </div>' +
                                '   </div>' +
                                '</a>';
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