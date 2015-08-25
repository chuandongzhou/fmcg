@extends('master')


@section('title')@yield('subtitle') | 批发商管理@stop


@section('css')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@stop


@section('header')
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">切换导航</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('admin') }}">快销系统</a>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
@stop


@section('body')
    <div class="container-fluid admin-container">
        <div class="row">
            <div class="col-sm-2">
                <div class="row left-container">
                    <div class="panel-group text-center" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-ten">
                                        返回首页
                                    </a>
                                </h4>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-one">
                                        终端订单管理
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-one" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                        <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-two">
                                        供应商订单管理
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-two" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="{{ url('/admin/user/create?type=wholesalers')  }}">批发商添加</a> <a
                                                href="{{ url('/admin/user?type=wholesalers')  }}"
                                                class="manger">管理</a></li>
                                        <li><a href="{{ url('/admin/user/create?type=retailer')  }}">终端商添加</a> <a
                                                href="{{ url('/admin/user?type=retailer')  }}" class="manger">管理</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-three">
                                        我的商品
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-three" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="{{  url('admin/category/create')  }}">商品详情</a> <a
                                                href="{{  url('admin/category')  }}" class="manger">管理</a></li>
                                        <li><a href="{{  url('admin/attr/create')  }}">商品编辑</a> <a
                                                href="{{  url('admin/attr')  }}" class="manger">管理</a></li>
                                        <li><a href="{{  url('admin/images/create')  }}">添加商品</a> <a
                                                href="{{  url('admin/images')  }}" class="manger">管理</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-four">
                                        我的收藏
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-four" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="{{ url('admin/advert-index/create') }}">商家收藏</a>
                                            <a href="{{ url('admin/advert-index') }}" class="manger">管理</a></li>
                                        <li><a href="{{ url('admin/advert-user/create') }}">商品收藏</a>
                                            <a href="{{ url('admin/advert-user') }}" class="manger">管理</a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-five">
                                        报表统计
                                    </a>
                                </h4>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-six">
                                        个人中心
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="right-container">
                    @yield('right-container')
                </div>
            </div>
        </div>
    </div><!--/.container-->
@stop


@section('js')
    <script src="{{ asset('js/admin.js') }}"></script>
@stop