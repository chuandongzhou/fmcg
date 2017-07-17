@extends('mobile.master')

@section('subtitle', '我的收藏')

@section('header')
    <div class="fixed-header fixed-item">
        <div class="row nav-top white-bg orders-details-header">
            <div class="col-xs-12 color-black">我的收藏</div>
        </div>
        <div class="row">
            <div class="col-xs-12 collection-tab-menu">
                <a class="shop active">商铺</a>
                <a href="{{ url('like/goods') }}" class="commodity">商品</a>
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m90">
        <div class="row collection-container">
            @foreach($shops as $shop)
                <a href="{{ url('shop/' . $shop->id) }}">
                    <div class="col-xs-12 clearfix shop-list-item">
                        <img src="{{ $shop->logo_url }}" class="pull-left"/>
                        <div class="opera-panel">
                            <div class="shop-name">
                                <b>{{ $shop->name }}</b>
                                <span class="prompt">（{{ cons()->valueLang('user.type', $shop->user_type) }}）</span>
                            </div>
                            <div class="amount">
                                <span class="prompt">最低配送额 :</span>
                                <span class="num">¥{{ $shop->min_money }}</span>
                            </div>
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
        function loadShops() {
            if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                var url = window.location.href, urls = url.split('?'), queryString = urls[1];
                queryString = queryString === undefined ? '' : queryString.replace(/[\&\?]*page=\d+/, '');
                loadingImage.removeClass('hidden');
                document.removeEventListener('touchmove', loadShops, false);
                var pivot = queryString ? '&' : '';
                queryString = queryString + pivot + 'page=' + (page + 1);
                $.ajax({
                    url: urls[0],
                    method: 'get',
                    data: queryString
                }).done(
                    function (data) {
                        loadingImage.before(data.html);
                        if (data.count) {
                            page = page + 1
                            loadingImage.addClass('hidden');
                            document.addEventListener('touchmove', loadShops, false);
                        } else {
                            loadingImage.html('没有更多数据')
                        }
                    }
                ).fail(function () {
                    loadingImage.addClass('hidden');
                    document.addEventListener('touchmove', loadShops, false);
                    showMassage('服务器错误');
                });

            }
        }
        document.addEventListener('touchmove', loadShops, false);

    </script>
@stop