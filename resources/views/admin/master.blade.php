@extends('master')


@section('title')@yield('subtitle') | 快销后台管理@stop


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
                                       href="#collapse-one">
                                        系统管理
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-one" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                        <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                        <li>
                                            <a href="{{url('admin/admin/create')}}">管理员添加</a>
                                            <a href="{{url('admin/admin')}}" class="manger">管理</a>
                                        </li>
                                        <li><a href="{{url('admin/admin/password')}}">密码修改</a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-two">
                                        账号管理
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
                                        <li><a href="{{ url('/admin/user/create?type=supplier')  }}">供应商添加</a> <a
                                                    href="{{ url('/admin/user?type=supplier')  }}" class="manger">管理</a>
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
                                        商品管理
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-three" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="{{  url('admin/category/create')  }}">商品分类添加</a> <a
                                                    href="{{  url('admin/category')  }}" class="manger">管理</a></li>
                                        <li><a href="{{  url('admin/attr/create')  }}">商品标签添加</a> <a
                                                    href="{{  url('admin/attr')  }}" class="manger">管理</a></li>
                                        <li><a href="{{  url('admin/images/create')  }}">商品图片添加</a> <a
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
                                        广告投放
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-four" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="{{ url('admin/advert-index/create') }}">首页广告添加</a>
                                            <a href="{{ url('admin/advert-index') }}" class="manger">管理</a></li>
                                        <li><a href="{{ url('admin/advert-user/create') }}">用户广告添加</a>
                                            <a href="{{ url('admin/advert-user') }}" class="manger">管理</a></li>
                                        <li><a href="{{ url('admin/advert-app/create') }}">APP广告添加</a>
                                            <a href="{{ url('admin/advert-app') }}" class="manger">管理</a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-five">
                                        平台交易
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-five" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">角色添加</a> <a href="#" class="manger">管理</a></li>
                                        <li><a href="#">菜单添加</a> <a href="#" class="manger">管理</a></li>
                                        <li><a href="#">管理员添加</a> <a href="#" class="manger">管理</a></li>
                                        <li><a href="#">密码修改</a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-six">
                                        客服中心
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-six" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">反馈信息</a></li>
                                        <li><a href="#">交易信息查询</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-seven">
                                        运营数据
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-seven" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">运营数据统计</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse-eight">
                                        推广管理
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-eight" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul>
                                        <li><a href="#">推广人员添加</a><a href="#" class="manger">管理</a></li>
                                    </ul>
                                </div>
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