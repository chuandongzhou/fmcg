@extends('mobile.master')

@section('subtitle', '店铺')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item">
        <div class="row nav-top margin-clear">
            <div class="col-xs-12 search-item">
                <form action="{{ url('shop') }}" method="get">
                    <div class="panel">
                        <i class="iconfont icon-search"></i>
                        <input type="text" class="search" name="name" value="{{ $name }}" placeholder="查找商铺"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid  m60 p65 shop-list">
        @foreach($shops as $shop)
            <div class="row shop-panel">
                <div class="col-xs-12 shop-item">
                    <a href="{{ url('shop/' . $shop->id) }}">
                        <img class="shop-img" src="{{ $shop->logo_url }}">
                        <div class="shop-msg-content">
                            <div class="shop-name"><b>{{ $shop->name }}</b><span
                                        class="prompt">({{ cons()->valueLang('user.type', $shop->user_type)  }})</span>
                            </div>
                            <div class="amount"><span class="prompt">配送额 :</span> ¥{{ $shop->min_money }}</div>
                            <div class="sales"><span class="prompt">商铺销量 :</span> {{ $shop->sales_volume }} <span
                                        class="prompt">共</span> {{ $shop->goods_count }}<span
                                        class="prompt">件商品</span></div>
                        </div>
                    </a>
                </div>
                <div class="col-xs-12 product-item pd-clear">
                    @foreach($shop->three_goods as $goods)
                        <div class="item">
                            <a href="{{ url('goods/' . $goods->id) }}">
                                <img class="lazy" data-original="{{ $goods->image_url }}"/>
                                <div class="product-name">{{ $goods->name }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="col-xs-12 text-center  loading-image hidden">
            <i class="fa fa-spinner fa-pulse"></i>
        </div>
    </div>
@stop

@include('mobile.includes.footer')

@section('js')
    @parent
    <script type="text/javascript">
        //滑到最底部时加载商品
        var loadingImage = $('.loading-image'), page = 1;
        function loadShops() {
            if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                var url = window.location.href, urls = url.split('?'), queryString = urls[1];
                queryString = queryString === undefined ? '' : queryString.replace(/[\&\?]*page=\d+/, '');
                loadingImage.removeClass('hidden');
                document.removeEventListener('touchmove', loadShops, false);
                var pivot = queryString ? '&' : '';
                queryString = queryString + pivot + 'page=' + (page + 1);
                $.ajax({
                    url: site.url('shop'),
                    method: 'get',
                    data: queryString
                }).done(
                    function (data) {
                        var shops = data.shops.data, shopHtml = '';
                        for (var i in shops) {
                            var item = shops[i],
                                threeGoods = item.three_goods,
                                userTypeName = item.user_type === '2' ? '批发商' : '供应商';
                            shopHtml += '<div class="row shop-panel">' +
                                '        <div class="col-xs-12 shop-item">' +
                                '   <a href="' + site.url('shop/' + item.id) + '">' +
                                '   <img class="shop-img" src="' + item.logo_url + '">' +
                                '   <div class="shop-msg-content">' +
                                '       <div class="shop-name">' +
                                '           <b>' + item.name + '</b>' +
                                '           <span class="prompt">(' + userTypeName + ')</span>' +
                                '</div>' +
                                '    <div class="amount"><span class="prompt">配送额 :</span> ¥' + item.min_money + '</div>' +
                                '   <div class="sales"><span class="prompt">商铺销量 :</span>' + item.sales_volume + '<span class="prompt">共</span> ' + item.goods_count + '<spanclass="prompt">件商品</span></div>' +
                                '    </div>' +
                                '    </a>' +
                                '    </div>' +
                                '    <div class="col-xs-12 product-item pd-clear">';
                            for (var j in threeGoods) {
                                var goods = threeGoods[j];
                                shopHtml += '<div class="item">' +
                                    ' <a href="' + site.url('goods/' + goods.id) + '">' +
                                    '   <img class="lazy" src="' + goods.image_url + '"/>' +
                                    '   <div class="product-name">' + goods.name + '</div>' +
                                    '   </a>' +
                                    '   </div>';
                            }
                            shopHtml += '   </div>' +
                                '</div>';

                        }
                        loadingImage.before(shopHtml);
                        if (shops.length) {
                            page = page + 1
                            loadingImage.addClass('hidden');
                            document.addEventListener('touchmove', loadShops, false);
                        } else {
                            loadingImage.html('没有更多数据')
                        }
                    }
                ).fail(function () {
                    showMassage('服务器错误');
                    loadingImage.addClass('hidden');
                    document.addEventListener('touchmove', loadShops, false);
                })

            }
        }
        document.addEventListener('touchmove', loadShops, false);

    </script>
@stop