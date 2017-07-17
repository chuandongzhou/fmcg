@extends('mobile.shop.master')

@section('subtitle', '店铺详情')

@include('includes.jquery-lazeload')

@section('body')
    @parent
    <div class="container-fluid  m185 p65">

        <div class="row sort-wrap">
            <div class="col-xs-12 pd-clear product-wrap">
                @foreach($goods as $item)

                    <div class="product-col">
                        <a href="{{ url('goods/' . $item->id) }}">
                            <img class="product-img lazy" data-original="{{ $item->image_url }}">
                            <span class="@if($item->is_out)prompt lack @elseif($item->is_promotion)prompt promotions @elseif($item->is_new)prompt new-listing @endif"></span>
                        </a>
                        <div class="product-info">
                            <div class="product-name">{{ $item->name }}</div>
                        </div>
                        <div class="clearfix">
                            <div class="pull-left product-price red">¥{{ $item->price . '/' . $item->pieces }}</div>
                            <a href="javascript:" class="join-cart pull-right mobile-ajax" data-method="post"
                               data-url="{{ url('api/v1/cart/add/' . $item->id) }}" data-done-then="none">
                                <i class="iconfont icon-gouwuche"></i>
                            </a>
                        </div>
                    </div>


                @endforeach
                <div class="col-xs-12 text-center  loading-image hidden">
                    <i class="fa fa-spinner fa-pulse"></i>
                </div>
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
                    url: site.url('shop/{{ $shop->id }}'),
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
        likeFunc();
    </script>
@stop