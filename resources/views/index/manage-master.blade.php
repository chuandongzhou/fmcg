@extends('master')
@include('includes.chat')
@section('title')@yield('subtitle') | 订百达@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <link href="{{ asset('css/css.css') }}" rel="stylesheet" type="text/css"/>
    {{--<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>--}}
    <link href="{{ asset('css/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{ asset('css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('css/blue.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    @stop
@section('header')
    <!--[if lt IE 9]>
    <div class="ie-warning alert alert-warning alert-dismissable fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        您的浏览器不是最新的，您正在使用 Internet Explorer 的一个<strong>老版本</strong>。 为了获得最佳的浏览体验，我们建议您选用其它浏览器。
        <a class="btn btn-primary" href="http://browsehappy.com/" target="_blank" rel="nofollow">立即升级</a>
    </div>
    <![endif]-->
    <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner ">
            <div class="page-logo">
                <a href="{{ url('/') }}">
                    <span class="link-index logo-default">订百达首页</span>
                </a>
                <div class="menu-toggler sidebar-toggler">
                    <!-- 折叠菜单按钮 -->
                </div>

            </div>

            <!-- BEGIN 响应式菜单切换 -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>

            <div class="page-top">
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!--帮助中心-->

                            <li class="dropdown dropdown-extended dropdown-notification" >
                                <a href="{{ url('help') }}" class="dropdown-toggle"  data-hover="dropdown"
                                   data-close-others="true" >
                                    <i class="fa fa-question-circle" title="帮助中心"></i>  &nbsp;
                                </a>
                            </li>

                        <!--购物车-->
                        @if($carts = (new \App\Services\CartService)->cartDetail())
                        <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                            <a href="{{ url('cart') }}" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="fa fa-shopping-cart"></i>
                                @if( $carts['count']>0)
                                    <span class="badge badge-default">{{ $carts['count'] }} </span>
                                @endif

                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3> 最新加入商品</h3>
                                    <a href="{{ url('cart') }}">去购物车查看</a>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                        @foreach($carts['detail'] as $cart)
                                            <li>
                                                <a href="{{ url('goods/'.$cart->goods->id) }}">
                                                    <!--<span class="time">just now</span>-->
                                                <span class="details">
                                                    <span class="label">
                                                        <img src="{{ $cart->goods->image_url }}">
                                                    </span> {{ $cart->goods->name }}
                                                </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <!--最新消息-->
                        <li class="dropdown dropdown-extended dropdown-inbox quick-sidebar-toggler drop-newmsg" >
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                               data-close-others="true">
                                <i class="fa fa-commenting-o "></i>
                                <span class="badge badge-default total-message-count">0 </span>
                            </a>
                            <br>
                        </li>
                        <!--登录名-->
                        <li class="dropdown dropdown-user">
                            <a href="{{ url('personal/info') }}" class="dropdown-toggle"  data-hover="dropdown"
                               data-close-others="true">
                                <img alt="" class="img-circle" src="{{ $user->shop->logo }}"/>
                                <span class="username username-hide-on-mobile">{{ $user->shop->name }}</span>
                            </a>
                        </li>
                        <!--退出登录-->
                        <li class="dropdown dropdown-extended drop-exit ">
                            <a href="{{ url('auth/logout') }}">
                                <i class="icon-logout"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
@stop

@section('body')
    <div class="page-container public-personal contents">
            @yield('container')
            <div class="msg-channel" id="alert-div">
                <p class="title"><span class="pull-left">你有新消息</span><a class="close-btn fa fa-remove pull-right"></a></p>
                <a class="check" href="#">点击查看>>>></a>
            </div>
        <!--登出按钮-->
        <a href="javascript:;" class="page-quick-sidebar-toggler">
            <i class="icon-login"></i>
        </a>
        <!--登出按钮下的聊天-->
        <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
            <div class="page-quick-sidebar">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab"> {{ $user->shop->name }}
                            <span class="badge badge-danger total-message-count">0</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                        <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd"
                             data-wrapper-class="page-quick-sidebar-list">
                            {{--<ul class="media-list list-items">--}}
                                {{--<li class="media">--}}
                                    {{--<div class="media-status">--}}
                                        {{--<span class="badge badge-success">8</span>--}}
                                    {{--</div>--}}
                                    {{--<img class="media-object" src="http://placehold.it/100" alt="...">--}}

                                    {{--<div class="media-body">--}}
                                        {{--<h4 class="media-heading">Bob Nilson</h4>--}}

                                        {{--<div class="media-heading-sub"> Project Manager</div>--}}
                                    {{--</div>--}}
                                {{--</li>--}}
                            {{--</ul>--}}
                            {{--<ul class="media-list list-items">--}}
                                {{--<li class="media">--}}
                                    {{--<div class="media-status">--}}
                                        {{--<span class="badge badge-warning">2</span>--}}
                                    {{--</div>--}}
                                    {{--<img class="media-object" src="http://placehold.it/100" alt="...">--}}

                                    {{--<div class="media-body">--}}
                                        {{--<h4 class="media-heading">Lara Kunis</h4>--}}

                                        {{--<div class="media-heading-sub"> CEO, Loop Inc</div>--}}
                                        {{--<div class="media-heading-small"> Last seen 03:10 AM</div>--}}
                                    {{--</div>--}}
                                {{--</li>--}}
                            {{--</ul>--}}
                        </div>
                        <div class="page-quick-sidebar-item">
                            <div class="page-quick-sidebar-chat-user">
                                <div class="page-quick-sidebar-nav">
                                    <a href="javascript:;" class="page-quick-sidebar-back-to-list">
                                        <i class="icon-arrow-left"></i>Back</a>
                                </div>
                                <div class="page-quick-sidebar-chat-user-messages">
                                    <div class="post out">
                                        <img class="avatar" alt="" src="http://placehold.it/100"/>

                                        <div class="message">
                                            <span class="arrow"></span>
                                            <a href="javascript:;" class="name">Bob Nilson</a>
                                            <span class="datetime">20:15</span>
                                            <span class="body"> When could you send me the report ? </span>
                                        </div>
                                    </div>
                                    <div class="post in">
                                        <img class="avatar" alt="" src="http://placehold.it/100"/>

                                        <div class="message">
                                            <span class="arrow"></span>
                                            <a href="javascript:;" class="name">Ella Wong</a>
                                            <span class="datetime">20:15</span>
                                            <span class="body"> Its almost done. I will be sending it shortly </span>
                                        </div>
                                    </div>
                                    <div class="post out">
                                        <img class="avatar" alt="" src="http://placehold.it/100"/>

                                        <div class="message">
                                            <span class="arrow"></span>
                                            <a href="javascript:;" class="name">Bob Nilson</a>
                                            <span class="datetime">20:15</span>
                                            <span class="body"> Alright. Thanks! :) </span>
                                        </div>
                                    </div>
                                    <div class="post in">
                                        <img class="avatar" alt="" src="http://placehold.it/100"/>

                                        <div class="message">
                                            <span class="arrow"></span>
                                            <a href="javascript:;" class="name">Ella Wong</a>
                                            <span class="datetime">20:16</span>
                                            <span class="body"> You are most welcome. Sorry for the delay. </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-quick-sidebar-chat-user-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Type a message here...">

                                        <div class="input-group-btn">
                                            <button type="button" class="btn green">
                                                <i class="icon-paper-clip"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane page-quick-sidebar-alerts" id="quick_sidebar_tab_2">
                        <div class="page-quick-sidebar-alerts-list">
                            <h3 class="list-heading">General</h3>
                            <ul class="feeds list-items">
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 4 pending tasks.
                                                        <span class="label label-sm label-warning "> Take action
                                                            <i class="fa fa-share"></i>
                                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> Just now</div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <div class="col1">
                                            <div class="cont">
                                                <div class="cont-col1">
                                                    <div class="label label-sm label-success">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                </div>
                                                <div class="cont-col2">
                                                    <div class="desc"> Finance Report for year 2013 has been released.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col2">
                                            <div class="date"> 20 mins</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-danger">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 5 pending membership that requires a quick
                                                    review.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 24 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> New order received with
                                                    <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 30 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-success">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 5 pending membership that requires a quick
                                                    review.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 24 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-bell-o"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> Web server hardware needs to be upgraded.
                                                    <span class="label label-sm label-warning"> Overdue </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 2 hours</div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <div class="col1">
                                            <div class="cont">
                                                <div class="cont-col1">
                                                    <div class="label label-sm label-default">
                                                        <i class="fa fa-briefcase"></i>
                                                    </div>
                                                </div>
                                                <div class="cont-col2">
                                                    <div class="desc"> IPO Report for year 2013 has been released.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col2">
                                            <div class="date"> 20 mins</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <h3 class="list-heading">System</h3>
                            <ul class="feeds list-items">
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 4 pending tasks.
                                                        <span class="label label-sm label-warning "> Take action
                                                            <i class="fa fa-share"></i>
                                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> Just now</div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <div class="col1">
                                            <div class="cont">
                                                <div class="cont-col1">
                                                    <div class="label label-sm label-danger">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                </div>
                                                <div class="cont-col2">
                                                    <div class="desc"> Finance Report for year 2013 has been released.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col2">
                                            <div class="date"> 20 mins</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-default">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 5 pending membership that requires a quick
                                                    review.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 24 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> New order received with
                                                    <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 30 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-success">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> You have 5 pending membership that requires a quick
                                                    review.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 24 mins</div>
                                    </div>
                                </li>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-warning">
                                                    <i class="fa fa-bell-o"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> Web server hardware needs to be upgraded.
                                                    <span class="label label-sm label-default "> Overdue </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 2 hours</div>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <div class="col1">
                                            <div class="cont">
                                                <div class="cont-col1">
                                                    <div class="label label-sm label-info">
                                                        <i class="fa fa-briefcase"></i>
                                                    </div>
                                                </div>
                                                <div class="cont-col2">
                                                    <div class="desc"> IPO Report for year 2013 has been released.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col2">
                                            <div class="date"> 20 mins</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">
                        <div class="page-quick-sidebar-settings-list">
                            <h3 class="list-heading">General Settings</h3>
                            <ul class="list-items borderless">
                                <li> Enable Notifications
                                    <input type="checkbox" class="make-switch" checked data-size="small"
                                           data-on-color="success" data-on-text="ON" data-off-color="default"
                                           data-off-text="OFF"></li>
                                <li> Allow Tracking
                                    <input type="checkbox" class="make-switch" data-size="small" data-on-color="info"
                                           data-on-text="ON" data-off-color="default" data-off-text="OFF"></li>
                                <li> Log Errors
                                    <input type="checkbox" class="make-switch" checked data-size="small"
                                           data-on-color="danger" data-on-text="ON" data-off-color="default"
                                           data-off-text="OFF"></li>
                                <li> Auto Sumbit Issues
                                    <input type="checkbox" class="make-switch" data-size="small" data-on-color="warning"
                                           data-on-text="ON" data-off-color="default" data-off-text="OFF"></li>
                                <li> Enable SMS Alerts
                                    <input type="checkbox" class="make-switch" checked data-size="small"
                                           data-on-color="success" data-on-text="ON" data-off-color="default"
                                           data-off-text="OFF"></li>
                            </ul>
                            <h3 class="list-heading">System Settings</h3>
                            <ul class="list-items borderless">
                                <li> Security Level
                                    <select class="form-control input-inline input-sm input-small">
                                        <option value="1">Normal</option>
                                        <option value="2" selected>Medium</option>
                                        <option value="e">High</option>
                                    </select>
                                </li>
                                <li> Failed Email Attempts
                                    <input class="form-control input-inline input-sm input-small" value="5"/></li>
                                <li> Secondary SMTP Port
                                    <input class="form-control input-inline input-sm input-small" value="3560"/></li>
                                <li> Notify On System Error
                                    <input type="checkbox" class="make-switch" checked data-size="small"
                                           data-on-color="danger" data-on-text="ON" data-off-color="default"
                                           data-off-text="OFF"></li>
                                <li> Notify On SMTP Error
                                    <input type="checkbox" class="make-switch" checked data-size="small"
                                           data-on-color="warning" data-on-text="ON" data-off-color="default"
                                           data-off-text="OFF"></li>
                            </ul>
                            <div class="inner-content">
                                <button class="btn btn-success">
                                    <i class="icon-settings"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('footer')
        <footer class="panel-footer footer page-footer">
            <div class="container text-center text-muted">
                <div class="row text-center">
                    <div class="col-xs-6">
                        <ul class="list-inline">
                            <li><a href="{{ url('about') }}" class="icon about">关于我们</a></li>
                            <li>
                                <div class="contact-panel">
                                    <a href="javascript:;" class="icon contact-information">联系方式</a>
                                </div>
                                <div class="contact-content content hidden">
                                    <div>{{ cons('system.company_tel') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_mobile') }}</div>
                                    <div>{{ cons('system.company_addr') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="feedback-panel">
                                    <a class="feedback icon" href="javascript:;">意见反馈</a>
                                </div>
                                <div class="content hidden">
                                    <form class="ajax-form" method="post" action="{{ url('api/v1/feedback') }}"
                                          accept-charset="UTF-8" data-help-class="error-msg text-center"
                                    >
                                        <div>
                                            <textarea placeholder="请填写您的反馈意见" name="content"></textarea>
                                        </div>
                                        <div>
                                            <div class="input-group">
                                            <span class="input-group-addon" id="feedback-contact"><i
                                                        class="fa fa-envelope-o"></i></span>
                                                <input type="text" class="form-control" placeholder="留个邮箱或者别的联系方式呗"
                                                       aria-describedby="feedback-contact" name="contact">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-submit" type="submit"
                                                        data-done-then="none" data-done-text="反馈提交成功">提交
                                                </button>
                                            </span>
                                            </div>
                                            <!-- /input-group -->
                                        </div>
                                    </form>
                                </div>
                            </li>
                            <li>
                                <div id="qr-content-panel">
                                    <a href="javascript:;" class="app-down icon">APP下载</a>
                                </div>
                                <div class="content hidden">
                                    <div class="qr-panel">
                                        <div class="dbd item">
                                            <div class="qr-code dbd-qr-code"></div>
                                            <div class="text text-center">订百达</div>
                                        </div>
                                        <div class="driver-helper item">
                                            <div class="qr-code helper"></div>
                                            <div class="text text-center">司机助手</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-6">
                        <p>Copyright {!! cons('system.company_name') !!}</p>
                    </div>
                </div>
            </div>
        </footer>

@stop

@section('js-lib')
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
    <script src="{{ asset('js/index.js?v=1.0.0') }}"></script>
    <script src="{{ asset('js/ajax-polling.js') }}"></script>
    <script src="{{ asset('js/js.cookie.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.blockui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/morris.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/echarts.common.min.js') }}"></script>
@stop

@section('js')
    <script type="text/javascript">
        $(function () {
            //意见反馈
            $('.feedback-panel > a').popover({
                container: '.feedback-panel',
                placement: 'top',
                html: true,
                content: function () {
                    return $(this).parent().siblings('.content').html();
                }
            });

            //扫二维码下载app
            tooltipFunc('#qr-content-panel > a', '#qr-content-panel');
            //联系方式
            tooltipFunc('.contact-panel > a', '.contact-panel');

            //调用tooltip插件
            function tooltipFunc(item, container) {
                $(item).tooltip({
                    container: container,
                    placement: 'top',
                    html: true,
                    title: function () {
                        return $(this).parent().siblings('.content').html();
                    }
                })
            }

        });
    </script>
@stop
