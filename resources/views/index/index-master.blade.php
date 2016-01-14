@extends('index.master')

@section('header')
    @parent
    @include('index.search')
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
                    <li class="menu-list" id="menu-list">
                        <a href="#" class="menu-wrap-title list-name">商品分类</a>

                        <div class="menu-list-wrap">
                            <div class="categories" id="other-page-categories">
                                <ul class="menu-wrap">
                                    @foreach($categories as $category)
                                        <li><a class="one-title"
                                               href="{{ url('search?category_id=1'. $category['id']) }}"><i></i>{{ $category['name'] }}
                                            </a>

                                            <div class="menu-down-wrap menu-down-layer">
                                                @foreach($category['child'] as $child)
                                                    <div class="item active">
                                                        <h3 class="title"><a
                                                                    href="{{ url('search?category_id=2'. $child['id']) }}">{{ $child['name'] }}</a>
                                                        </h3>
                                                        @foreach($child['child'] as $grandChild)
                                                            <a href="{{ url('search?category_id=3'. $grandChild['id']) }}">{{ $grandChild['name'] }}</a>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="active"><a class="list-name" href="{{ url('/') }}">首页</a></li>
                    @if(auth()->user()->type == cons('user.type.retailer'))
                        <li><a href="{{ url('shop?type=wholesaler') }}" class="btn list-name">批发商</a></li>
                    @endif
                    <li><a href="{{ url('shop?type=supplier') }}" class="btn list-name">供应商</a></li>
                </ul>
            </div>
        </div>
    </nav>

@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            if (!Cookies.get('province_id')) {
                setProvinceName();
            }
        })
    </script>
@stop
