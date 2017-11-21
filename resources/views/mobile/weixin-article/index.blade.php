@extends('mobile.master')

@section('subtitle', '文章')

@section('css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/swiper.min.css') }}">
@stop

@section('body')
    @parent
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 pd-clear">
                <!-- 轮播图 -->
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($banners as $banner)
                            <div class="swiper-slide">
                                <a href="{{ $banner->link_url }}">
                                    <img src="{{ $banner->image_url }}" alt=""/>
                                    <div class="slide-title">
                                        <div>{{ $banner->title }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="col-xs-12  article-list pd-right-clear">
                @foreach($articles as $article)
                    <div class="article-item">
                        <a href="{{ $article->link_url }}">
                            <div class="img pull-left"><img src="{{ $article->image_url }}" alt=""/></div>
                            <div class="article-title">
                                <p>{{ $article->title }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="col-xs-12 text-center  loading-image hidden">
                <i class="fa fa-spinner fa-pulse"></i>
            </div>
        </div>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/swiper.min.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        var swiper = new Swiper('.swiper-container', {
            autoplay: 3000,
            pagination: '.swiper-pagination',
            paginationClickable: true
        });

        //滑到最底部时加载商品
        var loadingImage = $('.loading-image'), page = 1;

        function loadArticles() {
            if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
                var url = window.location.href, urls = url.split('?'), queryString = urls[1];
                queryString = queryString === undefined ? '' : queryString.replace(/[\&\?]*page=\d+/, '');
                loadingImage.removeClass('hidden');
                document.removeEventListener('touchmove', loadArticles, false);
                var pivot = queryString ? '&' : '';
                queryString = queryString + pivot + 'page=' + (page + 1);
                $.ajax({
                    url: site.url('weixin-article/articles'),
                    method: 'get',
                    data: queryString
                }).done(function (data) {
                        var articles = data.articles, html = '';
                        for (var i in articles) {
                            var item = articles[i];
                            html += '<div class="article-item">' +
                                '   <a href="' + item['link_url'] + '">' +
                                '           <div class="img pull-left"><img src="' + item['image_url'] + '" alt=""/></div>' +
                                '           <div class=" article-title">' +
                                '               <p>' + item['title'] + '</p>' +
                                '           </div>' +
                                '       </a>' +
                                '   </div>';
                        }

                        $('.article-list').append(html);
                        if (articles.length) {
                            page = page + 1;
                            loadingImage.addClass('hidden');
                            document.addEventListener('touchmove', loadArticles, false);
                        } else {
                            loadingImage.html('没有更多数据')
                        }
                    }
                ).fail(function () {
                    showMassage('服务器错误');
                    loadingImage.addClass('hidden');
                    document.addEventListener('touchmove', loadArticles, false);
                })

            }
        }

        document.addEventListener('touchmove', loadArticles, false);

    </script>
@stop