<nav class="navbar top-header dealer-header">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed navbar-button" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand logo-icon" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}">
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <form action="{{ url('search') }}" class="navbar-form navbar-left search text-center" role="search" method="get">
                <div class="input-group">
                    <div class="select-role pull-left">
                        <a href="javascript:void(0)" class="selected"><span>商品</span><i
                                    class="fa fa-angle-down"></i></a>
                        <ul class="select-list">
                            <li class="hide"><a href="javascript:void(0)" data-url="search">商品</a></li>
                            @if((isset($user) && $user->type == cons('user.type.retailer')) || is_null($user))
                                <li><a href="javascript:void(0)" data-type="wholesaler">批发商</a></li>
                            @endif
                            <li><a href="javascript:void(0)" data-type="supplier">供应商</a></li>
                        </ul>
                    </div>
                    <input type="text" name="name" value="{{ isset($data) ? array_get($data, 'name') : '' }}" class="control pull-right" aria-describedby="course-search">
                    <span class="input-group-btn btn-primary">
                        <button class="btn btn-primary search-btn" type="submit">搜索</button>
                    </span>
                </div>
                @if ($keywords)
                    <div class="text-right search-keyword">
                        @foreach($keywords as $key=>$val)
                            <a href="{{ url('search?name=' . $key) }}">{{ substr($key,0,6) }}</a>
                        @endforeach
                    </div>
                @endif
            </form>
            <ul class="nav navbar-nav navbar-right right-btn">
                <li><a class="btn btn-danger shopping-car" href="{{ url('cart') }}"><i class="fa fa-shopping-cart"></i> 购物车 <span
                                class="badge">{{ $cartNum }}</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>