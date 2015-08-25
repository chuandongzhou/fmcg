@extends('index.master')
@include('includes.timepicker')
@section('container')
        <div class="container wholesalers-top-header">
            <div class="col-sm-4 logo">
                <a class="logo-icon">LOGO</a>
            </div>
            <div class="col-sm-4 col-sm-push-4 right-search">
                <form class="search" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" aria-describedby="course-search">
                <span class="input-group-btn btn-primary">
                    <button class="btn btn-primary" type="submit">搜本店</button>
                </span>
                    </div>
                </form>
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
                        <li class="active"><a href="#">首页</a></li>
                        <li class="menu-wrap">
                            <a href="#" class="menu-hide item menu-wrap-title">商品分类</a>
                            <ul class="a-menu">
                                <li>
                                    <a href="#" class="menu-hide item">酒水饮料</a>
                                    <ul class="secondary-menu">
                                        <li class="second-menu-item"><a href="#" class="item">酒水饮料2</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                        <li class="second-menu-item"><a href="#" class="item">酒水饮料2</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                        <li class="second-menu-item"><a href="#" class="item">酒水饮料2</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="#" class="menu-hide item">休闲食品</a>
                                    <ul class="secondary-menu">
                                        <li class="second-menu-item"><a href="#" class="item">休闲食品</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                        <li class="second-menu-item"><a href="#" class="item">休闲食品</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                        <li class="second-menu-item"><a href="#" class="item">休闲食品</a>

                                            <div class="three-menu">
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 |</a>
                                                <a href="#">酒水饮料 </a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="#" class="menu-hide item">调味品</a></li>
                            </ul>

                        </li>
                        <li><a href="#">店家信息</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="right"><a href="#">控制台</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container my-goods wholesalers-management">
            <div class="row">
                <div class="col-sm-2 menu">
                    <a class="go-back" href="#">< 返回首页</a>
                    <ul class="menu-list">
                        <li><a href="#"><span class=""></span>订单管理</a></li>
                        <li><a href="#">我的商品</a></li>
                        <li><a href="#">订单统计</a></li>
                        <li><a href="#">个人中心</a></li>
                    </ul>
                </div>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-12 notice-bar">
                            <a href="#" class="btn btn-primary">待确认1</a>
                            <a href="#" class="btn">待收货2</a>
                            <a href="#" class="btn">待付款3</a>
                        </div>
                        <div class="col-sm-8 pay-detail">
                    <span class="item">支付方式 :
                        <select name="pay_type" class="ajax-get">
                            @foreach($pay_type as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="item">
                        订单状态 :
                          <select name="status" class="ajax-get">
                              @foreach($pay_type as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                              @endforeach
                          </select>
                        <input type="hidden" id="target-url" value="{{ url('wholesaler/admin/search') }}" />
                    </span>
                    <span class="item">
                        时间段 :
                        <input type="text" class="datetimepicker" placeholder="开始时间" name="start_at" />　至　
                        <input type="text" class="datetimepicker" placeholder="结束时间" name="end_at" />
                    </span>
                        </div>
                        <div class="col-sm-4 right-search search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="终端商、订单号" aria-describedby="course-search">
                        <span class="input-group-btn btn-primary">
                            <button class="btn btn-primary" type="submit">搜索</button>
                        </span>
                            </div>
                        </div>
                    </div>
                    <div class="row order-form-list">
                        <div class="col-sm-12 list-title">
                            <input type="checkbox">
                            <span class="time">2015年8月18日</span>
                            <span>订单号:100000000000000xx00</span>
                            <span>xxx终端商</span>
                        </div>
                        <div class="col-sm-8 list-content">
                            <ul>
                                <li>
                                    <img src="http://placehold.it/100">
                                    <a class="product-name" href="#">益力多 100ml*20瓶 活性乳酸菌饮品</a>
                                    <span class="red">￥60</span>
                                    <span>1</span>
                                </li>
                                <li>
                                    <img src="http://placehold.it/100">
                                    <a class="product-name" href="#">益力多 100ml*20瓶 活性乳酸菌饮品</a>
                                    <span class="red">￥60</span>
                                    <span>1</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-2 order-form-detail">
                            <p>订单状态 :未支付</p>
                            <p>支付方式 :在线支付</p>
                            <p>订单金额 :<span class="red">￥120</span></p>
                        </div>
                        <div class="col-sm-2 order-form-operating">
                            <p><a href="#" class="btn btn-primary">查看</a></p>
                            <p><a href="#" class="btn btn-danger">确认</a></p>
                            <p><a href="#" class="btn btn-success">导出</a></p>
                        </div>
                    </div>
                    <div class="row order-form-list">
                        <div class="col-sm-12 list-title">
                            <input type="checkbox">
                            <span class="time">2015年8月18日</span>
                            <span>订单号:100000000000000xx00</span>
                            <span>xxx终端商</span>
                        </div>
                        <div class="col-sm-8 list-content">
                            <ul>
                                <li>
                                    <img src="http://placehold.it/100">
                                    <a class="product-name" href="#">益力多 100ml*20瓶 活性乳酸菌饮品</a>
                                    <span class="red">￥60</span>
                                    <span>1</span>
                                </li>
                                <li>
                                    <img src="http://placehold.it/100">
                                    <a class="product-name" href="#">益力多 100ml*20瓶 活性乳酸菌饮品</a>
                                    <span class="red">￥60</span>
                                    <span>1</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-2 order-form-detail">
                            <p>订单状态 :未支付</p>
                            <p>支付方式 :在线支付</p>
                            <p>订单金额 :<span class="red">￥120</span></p>
                        </div>
                        <div class="col-sm-2 order-form-operating">
                            <p><a href="#" class="btn btn-primary">查看</a></p>
                            <p><a href="#" class="btn btn-danger">确认</a></p>
                            <p><a href="#" class="btn btn-success">导出</a></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary">查看</button>
                            <button class="btn btn-danger">确认</button>
                            <button class="btn btn-cancel">取消</button>
                            <button class="btn btn-success">导出</button>
                            <button class="btn btn-warning">发货</button>
                            <button class="btn btn-info">已收款</button>
                        </div>
                        <div class="col-sm-12 order-process">
                            <ul>
                                <li>订单状态流程 :</li>
                                <li>在线支付 :</li>
                                <li>未确认->(卖家确认操作)->未付款->(买家付款成功)->已付款->(卖家发货操作)->已发货->(买家收货操作)->已完成</li>
                                <li>货到付款 :</li>
                                <li>未确认->(卖家确认操作)->未发货->(卖家发货操作)->已发货(未收款)->(卖家收货操作)->已收货(未收款)->(卖家已收款操作)->已完成</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $('.datetimepicker').on('click',timepicker('.datetimepicker' , 'YYYY-MM-DD'));
            ajaxGetSelect();
        })
    </script>
@stop