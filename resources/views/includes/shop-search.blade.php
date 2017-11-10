@section('header')
    @parent
    <div class="container wholesalers-top-header">
        <div class="row">
            <div class="col-sm-1 logo">
                <a class="logo-icon" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}">
                </a>
            </div>
            @if(request()->is('goods/*'))
                <div class="col-sm-4 shop-header-wrap">
                    <select class="more-classify">
                        <option>更多分类</option>
                        @foreach($categories as $cate)
                            <option value="{{ url('search?category_id=' . $cate['level'] . $cate['id']) }}"> {{ $cate['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="col-sm-4 shop-header-wrap">
                    <div class="shop-name">
                        <h4 class="name">{{ $shop->name }}</h4>
                        <div class="operate">
                            @if($user->id==$shop->user_id)
                                <a href="javascript:" class="contact list-name" style="cursor:text"><span
                                            class="iconfont icon-kefu"></span> 联系客服</a>
                            @else
                                <a href="javascript:"
                                   onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                   class="contact list-name"><span class=" iconfont icon-kefu"></span> 联系客服</a>
                            @endif

                            <a href="javascript:void(0)" data-type="shops" data-method="post"
                               class="btn {{ $user->id==$shop->user_id?'':'btn-like' }} list-name like-shops"
                               data-id="{{ $shop->id }}"
                               style="cursor:{{ $user->id==$shop->user_id?'text':'pointer' }}">
                                @if(is_null($isLike))
                                    <i class="fa fa-star-o"></i> 加入收藏夹
                                @else
                                    <i class="fa fa-star"></i> 已收藏
                                @endif
                            </a>
                            @if($shop->user_type == cons('user.type.maker')  && !$haveRelation && !$applyed)
                                <a class="alt" data-id="{{ $shop->id }}" data-name="{{ $shop->name }}"
                                   href="javascript:;">
                                    <span class="iconfont icon-jiaoyi orange"></span>交易申请
                                </a>
                            @endif
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
            @endif


            <div class="col-sm-4  right-search">
                <form class="search search-form" role="search" autocomplete="off" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name"
                               value="{{ isset($data) ? array_get($data, 'name') : '' }}" placeholder="请输入商品名称"
                               aria-describedby="course-search">
                        <span class="input-group-btn ">
                        <button type="button" class="btn btn-primary search-btn search-shop">搜本店</button>
                         <button type="button" class="btn btn-primary search-site search-station">搜本站</button>
                    </span>

                    </div>
                    @if ($keywords)
                        <div class="text-left search-keyword">
                            @foreach($keywords as $key=>$val)
                                <a href="{{ url('shop/' . $shop->id . '/search?name=' . $key) }}">{{ substr($key,0,6) }}</a>
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
                        <h3 class="shop-name"
                            style="color:{{ $shop->ShopSignature?$shop->ShopSignature->color:'' }}">{{  $shop->ShopSignature?$shop->ShopSignature->text:'' }}</h3>
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
                        <a href="{{  request()->is('shop/all-goods/*','goods/*','shop/*/search*')?url('shop/' . $shop->id.'#rxp'):'#rxp' }}"
                           class="list-name">热销品</a></li>
                    <li>
                        <a href="{{  request()->is('shop/all-goods/*','goods/*','shop/*/search*')?url('shop/' . $shop->id.'#dptj'):'#dptj' }}"
                           class="list-name">店铺推荐</a></li>
                    <li>
                        <a href="{{  request()->is('shop/all-goods/*','goods/*','shop/*/search*')?url('shop/' . $shop->id.'#psdq'):'#psdq' }}"
                           class="list-name">配送地区</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="mask-outer" id="mask-outer">
        <div class="pop-general text-center maker-pop h200">
            <div class="pop-content">
                <a class="pull-right close-btn" href="javascript:"><i class="fa fa-remove"></i></a>
                <div class="pop-tips maker-wrap">
                    <span class="title-name"></span> <span class="maker"></span>
                </div>
                <input type="hidden" class="hidden-input" name="shopIds" value="">
                <div class="maker-msg">提交申请绑定您的平台账号信息</div>
                <div class="maker-msg"></div>
            </div>
            <div class="pop-footer-btn">
                <button class="btn btn-primary">提交申请</button>
            </div>
        </div>
    </div>

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

            $('.more-classify').change(function () {
                window.location.href = $(this).val();
            });

            //交易申请
            $(".alt").click(function () {
                var name = $(this).data('name');
                var shopId = $(this).data('id');
                bindTips(name, shopId, 'notRelation');
            });

            function bindTips(makerName, inputVal, type) {
                var type = type || 'applyed';
                var div = $('#mask-outer');
                var titleName = '', msg = '', btn = '';
                if (type == 'applyed') {
                    titleName = '已向厂家';
                    msg = '请您耐心等待...';
                    btn = '查看';
                } else {
                    titleName = '向厂家';
                    msg = '申请通过后才能进行购买';
                    btn = '提交申请';
                }
                div.find('span.title-name').html(titleName);
                div.find('span.maker').html(makerName);
                div.find('input.hidden-input').val(inputVal);
                div.find('div.maker-msg:eq(1)').html(msg);
                div.find('button.btn').html(btn);
                type == 'applyed' ? div.find('button.btn').addClass('applyed').removeClass('submit-apply') : div.find('button.btn').addClass('submit-apply').removeClass('applyed');
                div.show();
            }

            $('div.pop-footer-btn').on('click', '.submit-apply', (function () {
                var shopIds = $('input[name=shopIds]').val();
                if (shopIds != '') {
                    $.post(site.api('business/salesman-customer/apply-bind-relation'), {'shopIds': shopIds}, function (data) {
                        bindTips('{{implode(',',array_merge(session('notRelation') ?? [],session('applyed') ?? []))}}', '');
                    })
                }
            }));

            $('.pop-footer-btn').on('click', '.applyed', (function () {
                window.open('{{asset('business/trade-request')}}')
                $(".mask-outer").css("display", "none");
                $(".alt").css("display", "none");
            }));

            $('a.close-btn').on('click', function () {
                $(".mask-outer").css("display", "none");
                /*window.location.reload();*/
            });
        });
    </script>
@stop