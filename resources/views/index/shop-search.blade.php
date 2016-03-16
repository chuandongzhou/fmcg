@section('header')
    @parent
    <div class="container wholesalers-top-header">
        <div class="col-sm-4 logo">
            <a href="{{ url('/') }}" class="logo-icon"><img src="{{ asset('images/logo.png') }}"/></a>
        </div>
        @if ($shop->id == $user->shop->id)
            <div class="col-sm-4 col-sm-push-4 right-search">
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search"
                      autocomplete="off">
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary" type="submit">搜本店</button>
                </span>
                    </div>
                </form>
            </div>
        @else
            <div class="col-sm-4  right-search">
                <form action="{{ url('shop/' . $shop->id . '/search') }}" class="search" role="search"
                      autocomplete="off">
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" aria-describedby="course-search">
                        <span class="input-group-btn btn-primary">
                            <button class="btn btn-primary" type="submit">搜本店</button>
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
            <div class="col-sm-4 text-right shopping-car">
                <a href="{{ url('cart') }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> 购物车 <span
                            class="badge">{{ $cartNum }}</span></a>
            </div>
        @endif
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
                    <li class="active"><a class="list-name" href="{{ url('shop/' . $shop->id) }}">店家商品</a></li>
                    {{--<li class="menu-list" id="menu-list">--}}
                    {{--<a href="#" class="menu-wrap-title list-name">商品分类</a>--}}

                    {{--<div class="menu-list-wrap">--}}
                    {{--<div class="categories" id="other-page-categories">--}}
                    {{--<ul class="menu-wrap">--}}
                    {{--@foreach($categories as $category)--}}
                    {{--<li class="list1">--}}
                    {{--<a class="one-title"--}}
                    {{--href="{{ url('shop/' . $shop->id . '/search?category_id=1' . $category['id']) }}"><i></i>{{ $category['name'] }}--}}
                    {{--</a>--}}

                    {{--<div class="menu-down-wrap menu-down-layer">--}}
                    {{--@foreach($category['child'] as $child)--}}
                    {{--<div class="item active">--}}
                    {{--<h3 class="title">--}}
                    {{--<a href="{{ url('shop/'  . $shop->id . '/search?category_id=2' . $child['id']) }}">--}}
                    {{--{{ $child['name'] }}--}}
                    {{--</a>--}}
                    {{--</h3>--}}
                    {{--@foreach($child['child'] as $grandChild)--}}
                    {{--<a href="{{ url('shop/'  . $shop->id . '/search?category_id=3' . $grandChild['id']) }}">--}}
                    {{--{{ $grandChild['name'] }}--}}
                    {{--</a>--}}
                    {{--@endforeach--}}
                    {{--</div>--}}
                    {{--@endforeach--}}
                    {{--</div>--}}
                    {{--</li>--}}
                    {{--@endforeach--}}
                    {{--</ul>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--</li>--}}
                    <li><a class="list-name" href="{{ url('shop/' . $shop->id . '/detail') }}">店家信息</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if ($shop->id != $user->shop->id)
                        <li class="collect">
                            <a href="javascript:" onclick="window.open('{{ url('personal/message/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');" class="contact list-name"><span class="fa fa-commenting-o"></span> 联系客服</a>
                        </li>
                        <li class="collect">
                            <a href="javascript:void(0)" data-type="shops" data-method="post"
                               class="btn btn-like list-name" data-id="{{ $shop->id }}">
                                @if(is_null($isLike))
                                    <i class="fa fa-star-o"></i> 加入收藏夹
                                @else
                                    <i class="fa fa-star"></i> 已收藏
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@stop