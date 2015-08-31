@extends('index.master')
@include('includes.timepicker')
@section('container')
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
                            @if(isset($data['nonPayment']))
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/non-sure') }}">待确认{{ $data['nonSure'] }}</a>
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/non-payment') }}">待付款{{ $data['nonPayment'] }}</a>
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/non-arrived') }}">待收货{{ $data['nonArrived'] }}</a>
                            @else
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/non-sure') }}">待确认{{ $data['nonSure'] }}</a>
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/non-send') }}">待发货{{ $data['nonSend'] }}</a>
                                <a class="btn ajax-get"
                                   data-url="{{ url('order/pending-collection') }}">待收款{{ $data['pendingCollection'] }}</a>
                            @endif
                        </div>
                        <div class="col-sm-8 pay-detail">
                    <span class="item">支付方式 :
                        <select name="pay_type" class="ajax-select">
                            <option value="">全部方式</option>
                            @foreach($pay_type as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="item">
                        订单状态 :
                          <select name="status" class="ajax-select">
                              <option value="">全部状态</option>
                              @foreach($order_status as $key => $value)
                                  <option value="{{ $key }}">{{ $value }}</option>
                              @endforeach
                          </select>
                        <input type="hidden" id="target-url" value="{{ url('order/select') }}" />
                        <input type="hidden" id="search-role" value="{{ cons('user.type.retailer') }}" />
                    </span>
                    <span class="item">
                        时间段 :
                        <input type="text" class="datetimepicker" placeholder="开始时间" name="start_at" data-format="YYYY-MM-DD" />　至　
                        <input type="text" class="datetimepicker" id="end-time" placeholder="结束时间" name="end_at" data-format="YYYY-MM-DD" />
                    </span>
                        </div>
                        <div class="col-sm-4 right-search search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search_content" placeholder="终端商、订单号" aria-describedby="course-search">
                        <span class="input-group-btn btn-primary">
                            <button class="btn btn-primary ajax-submit" type="submit" data-url="{{ url('order/search') }}">搜索</button>
                        </span>
                            </div>
                        </div>
                    </div>
                    <form class="ajax-form" method="post">
                    <div class="content">
                        @foreach($orders['data'] as $order)
                        <div class="row order-form-list">
                            <div class="col-sm-12 list-title">
                                <input type="checkbox" name="orderIds[]" value="{{ $order['id'] }}">
                                <span class="time">{{ $order['created_at'] }}</span>
                                <span>订单号:100000000000000{{ $order['id'] }}</span>
                                <span>{{ $order['user']['user_name'] or $order['seller']['user_name'] }}</span>
                            </div>
                            <div class="col-sm-8 list-content">
                                <ul>
                                    @foreach($order['goods'] as $good)
                                    <li>
                                        <img src="{{ $good['image_url'] }}">
                                        <a class="product-name" href="#">{{ $good['name'] }}</a>
                                        <span class="red">￥{{ $good['pivot']['price'] }}</span>
                                        <span>{{ $good['pivot']['num'] }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-sm-2 order-form-detail">
                                <p>订单状态 :{{ $order['status_name'] }}</p>
                                <p>支付方式 :{{ $order['payment_type'] }}</p>
                                <p>订单金额 :<span class="red">￥{{ $order['price'] }}</span></p>
                            </div>
                            <div class="col-sm-2 order-form-operating">
                                <p><a href="#" class="btn btn-primary">查看</a></p>
                                <p><a class="btn btn-danger ajax" data-method = 'put' data-url="{{ url('order/sure/'.$order['id']) }}">确认</a></p>
                                <p><a href="#" class="btn btn-success">导出</a></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary">查看</button>
                            <button class="btn btn-danger ajax" data-url="{{ url('order/batch-sure') }}" data-method="put">确认</button>
                            <button class="btn btn-cancel">取消</button>
                            <button class="btn btn-success">导出</button>
                            <button class="btn btn-warning">发货</button>
                            <button class="btn btn-info">已收款</button>
                        </div>
                        <div class="col-sm-12 order-process page">
                            <ul>
                                <li>订单状态流程 :</li>
                                <li>在线支付 :</li>
                                <li>未确认->(卖家确认操作)->未付款->(买家付款成功)->已付款->(卖家发货操作)->已发货->(买家收货操作)->已完成</li>
                                <li>货到付款 :</li>
                                <li>未确认->(卖家确认操作)->未发货->(卖家发货操作)->已发货(未收款)->(卖家收货操作)->已收货(未收款)->(卖家已收款操作)->已完成</li>
                            </ul>
                        </div>
                    </div>
                    </form>
                </div>

            </div>
        </div>
@endsection
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            getOrderList();
        })
    </script>
@stop