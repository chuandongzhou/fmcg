@section('header')
    @parent
    <div class="container wholesalers-top-header">
        <div class="row">
            <div class="col-sm-1 logo">
                <a class="logo-icon" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}">
                </a>
            </div>
            <div class="col-sm-4 shop-header-wrap">
                <div class="shop-name">
                    <h4 class="name">{{ $shop->name }}</h4>
                    <div class="operate">
                        @if($user->id==$shop->user_id)
                            <a href="javascript:" class="contact list-name" style="cursor:text"><span
                                        class="fa fa-commenting-o"></span> 联系客服</a>
                        @else
                            <a href="javascript:"
                               onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                               class="contact list-name"><span class="fa fa-commenting-o"></span> 联系客服</a>
                        @endif

                        <a href="javascript:void(0)" data-type="shops" data-method="post"
                           class="btn {{ $user->id==$shop->user_id?'':'btn-like' }} list-name like-shops"
                           data-id="{{ $shop->id }}" style="cursor:{{ $user->id==$shop->user_id?'text':'pointer' }}">
                            @if(is_null($isLike))
                                <i class="fa fa-star-o"></i> 加入收藏夹
                            @else
                                <i class="fa fa-star"></i> 已收藏
                            @endif
                        </a>
                    </div>
                </div>
                <div class="shop-detail-popup">
                    <div class="popup-name">
                        <span class="prompt">店铺名称: </span>
                        {{ $shop->name }}
                    </div>
                    <div class="contact-information">
                        <ul class="item">
                            <i class="iconfont icon-lianxiren"></i>
                            <li>
                                <span class="prompt">联系人</span>
                                <span>{{ $shop->contact_person }}</span>
                            </li>
                        </ul>
                        <ul class="item">
                            <i class="iconfont icon-dianhua"></i>
                            <li>
                                <span class="prompt">联系方式</span>
                                <span>{{ $shop->contact_info }}</span>
                            </li>
                        </ul>
                        <ul class="item">
                            <i class="iconfont icon-peisong"></i>
                            <li>
                                <span class="prompt">最低配送额</span>
                                <span>¥{{ $shop->min_money }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-address">
                        <ul>
                            <i class="iconfont icon-lianxidizhi"></i>
                            <li>
                                <span class="prompt">店家地址</span>
                                <span>{{ $shop->address }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="store-introduction">
                        <div class="prompt">店家介绍 :</div>
                        <p class="content">{{ $shop->introduction }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 qr-code-wrap">
                <img src="{{ asset('images/qr-code-show.png') }}" class="code-show">
                <div class="shop-code">
                    <img src="{{ (new \App\Services\ShopService())->qrcode($shop->id,150) }}">
                    <span>扫一扫 进入手机店铺</span>
                </div>
            </div>
            <div class="col-sm-4  right-search">
                <form class="search search-form" role="search" autocomplete="off" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="请输入商品名称"
                               aria-describedby="course-search">
                    <span class="input-group-btn ">
                        <button type="button" class="btn btn-primary search-btn search-shop">搜本店</button>
                         <button type="button" class="btn btn-primary search-site search-station">搜本站</button>
                    </span>

                    </div>
                    @if ($keywords)
                        <div class="text-left search-keyword">
                            @foreach($keywords as $key=>$val)
                                <a href="{{ url('shop/' . $shop->id . '/search?name=' . $key) }}">{{ $key }}</a>
                            @endforeach
                        </div>
                    @endif
                </form>
            </div>
            <div class="col-sm-2 text-right shopping-car">
                <a class="btn btn-primary" href="{{ url('cart') }}"><i class="fa fa-shopping-cart"></i> 购物车
                    <span>{{ $cartNum }}</span></a>
            </div>
        </div>
    </div>
    {{-- 店招 --}}
    <div class="container-fluid shop-pictures padding-clear">
        <div class="shop-pictures">
            <img src="{{ $shop->ShopSignature?$shop->ShopSignature->signature_url:asset('images/signature.jpg') }}">
            <div class="container">
                <div class="row margin-clear">
                    <div class="col-sm-12 padding-clear">
                        <h3 class="shop-name" style="color:{{ $shop->ShopSignature?$shop->ShopSignature->color:'' }}">{{  $shop->ShopSignature?$shop->ShopSignature->text:'' }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-default wholesalers-header">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('shop/all-goods/'.$shop->id) }}" class="list-name active">所有商品</a></li>
                    <li class="menu-list" id="menu-list">
                        <a href="#" class="menu-wrap-title list-name">商品分类</a>
                        <div class="menu-list-wrap">
                            <div class="categories" id="other-page-categories">
                                <ul class="menu-wrap">
                                    @foreach(\App\Services\CategoryService::formatShopGoodsCate($shop) as $category)
                                        <li class="list1">
                                            <a class="one-title"
                                               href="{{ url('shop/' . $shop->id . '/search?category_id=1' . $category['id']) }}">

                                                <i class="iconfont icon-{{ pinyin($category['name'])[0].pinyin($category['name'])[1] }} "></i>
                                                {{ $category['name'] }}
                                            </a>
                                            @if(isset($category['child']))
                                                <div class="menu-down-wrap menu-down-layer">
                                                    @foreach($category['child'] as $child)
                                                        <div class="item active">
                                                            <h3 class="title">
                                                                <a href="{{ url('shop/'  . $shop->id . '/search?category_id=2' . $child['id']) }}">
                                                                    {{ $child['name'] }}
                                                                </a>
                                                            </h3>
                                                            @foreach($child['child'] as $grandChild)
                                                                <a href="{{ url('shop/'  . $shop->id . '/search?category_id=3' . $grandChild['id']) }}">
                                                                    {{ $grandChild['name'] }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li><a class="list-name" href="{{ url('shop/' . $shop->id) }}">店铺首页</a></li>
                    <li>
                        <a href="{{  request()->is('shop/all-goods/*','goods/*')?url('shop/' . $shop->id.'#rxp'):'#rxp' }}"
                           class="list-name">热销品</a></li>
                    <li>
                        <a href="{{  request()->is('shop/all-goods/*','goods/*')?url('shop/' . $shop->id.'#dptj'):'#dptj' }}"
                           class="list-name">店铺推荐</a></li>
                    <li>
                        <a href="{{  request()->is('shop/all-goods/*','goods/*')?url('shop/' . $shop->id.'#psdq'):'#psdq' }}"
                           class="list-name">配送地区</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <script>
        $(function () {
            formSubmitByGet();
            $('.search-shop').click(function () {
                checksubmit(site.url('shop/' + "{{ $shop->id }}" + '/search'));
            });
            $('.search-station').click(function () {
                checksubmit(site.url('search'));
            });
            function checksubmit(url) {
                $("form").attr('action', url);
                $('form').submit();
            }
        });
    </script>
@stop