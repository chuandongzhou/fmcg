@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '订单统计')
@if(request()->input('obj_type')==3 && auth()->user()->type != cons('user.type.retailer'))
@section('top-title')
    <a href="{{ url('order-buy') }}">进货管理</a> >
    <span class="second-level">订单统计</span>
@stop
@else
@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> >
    <span class="second-level">订单统计</span>
@stop
@endif



@section('right')
    <div class="row my-goods order-report margin-clear">
        <div class="col-sm-12 content">
            <form action="{{ url('order/statistics') }}" method="get" autocomplete="off">
                <div class="col-sm-12 enter-item">
                    <input class="enter datetimepicker" name="start_at"
                           placeholder="{{ empty($search['start_at'])? '开始时间' : $search['start_at']}}" type="text"
                           value="{{ $search['start_at'] or '' }}">至
                    <input class="enter datetimepicker" name="end_at"
                           placeholder="{{ empty($search['end_at']) ? '结束时间' : $search['end_at']}}" type="text"
                           value="{{ $search['end_at'] or '' }}">
                    <select class="enter" name="pay_type">
                        <option value="">全部方式</option>
                        @foreach($pay_type as $key => $value)
                            <option value="{{ $key }}" {{ ($key==(isset($search['pay_type']) ? $search['pay_type'] : '')) ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if(auth()->user()->type  == cons('user.type.wholesaler'))
                        <input type="hidden" name="obj_type" value="{{ $search['obj_type'] or '' }}"/>
                    @else
                        <select class="enter" name="obj_type">
                            <option value="">全部订单对象</option>

                            @foreach($obj_type as $key => $value)
                                <option value="{{ $key }}" {{ ($key==(isset($search['obj_type']) ? $search['obj_type'] : '')) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" class="enter" name="goods_name" placeholder="商品名称"
                           value="{{ $search['goods_name'] or '' }}">
                    <input type="text" class="enter" name="user_name"
                           placeholder="{{ $statisticsType  ==  'buyer' ? '卖家名称' : '买家名称' }}"
                           value="{{ $search['user_name'] or '' }}">
                    <input type="hidden" name="obj_type" value="">
                    <button id="submitBtn" class="btn btn-blue-lighter search-by-get" type="submit">搜索</button>
                    @unless(empty($statistics))
                        <a id="export" href="{{ url('order/stat-export?'.  http_build_query($search)) }}"
                           class="btn btn-border-blue">统计导出</a>
                    @endunless
                </div>
                <div class="col-sm-12 enter-item">
                    <div class="item display-goods">
                        是否显示商品
                        <input type="hidden" id="show-goods-name-inp" name="show_goods_name"
                               value="{{ $search['show_goods_name'] ?1:0 }}"/>
                        <div class="btn-tabs">
                            <button type="button"
                                    class="show-goods-name btn yes {{ $search['show_goods_name'] ? 'active' : '' }}">是
                            </button>
                            <button type="button"
                                    class="no-show-goods-name btn no {{ $search['show_goods_name'] ? '' : 'active' }}">否
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 table-responsive tables">
                    <table class="table-bordered table">
                        <thead>
                        <tr>
                            <td>订单号</td>
                            <td>商家名称</td>
                            <td>支付方式</td>
                            <td>订单状态</td>
                            <td>创建时间</td>
                            <td>订单金额</td>
                            @if($search['show_goods_name'])
                                <td>商品编号</td>
                                <td>商品名称</td>
                                <td>商品单价</td>
                                <td>商品数量</td>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($statistics as $order)
                            @if($search['show_goods_name'])
                                @foreach($order['goods'] as $key => $value)
                                    <tr>
                                        @if(!$key)
                                            <td rowspan="{{ $order->goods->count() }}">{{ $order['id'] }}</td>
                                            <td rowspan="{{ $order->goods->count() }}">
                                                @if(auth()->user()->type == cons('user.type.wholesaler')&&$objCurrentType<cons('user.type.wholesaler') ||auth()->user()->type == cons('user.type.supplier'))
                                                    {{ $order['user']['shop']['name'] }}
                                                @else
                                                    {{ $order['shop']['name'] }}
                                                @endif
                                            </td>
                                            <td rowspan="{{ $order->goods->count() }}">{{ $order['payment_type'] }}</td>
                                            <td rowspan="{{ $order->goods->count() }}">{{ $order['status_name'] }}</td>
                                            <td rowspan="{{ $order->goods->count() }}">{{ $order['created_at'] }}</td>
                                            <td rowspan="{{ $order->goods->count() }}">￥{{ $order['price'] }}</td>
                                        @endif
                                        <td>{{ $value['id'] }}</td>
                                        <td>{{ $value['name'] }}</td>
                                        <td>￥{{ $value['pivot']['price'] }}</td>
                                        <td>{{ $value['pivot']['num'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    <td>
                                        {{ $statisticsType  ==  'buyer' ?  $order['shop']['name'] : $order->user_shop_name }}
                                    </td>
                                    <td>{{ $order['payment_type'] }}</td>
                                    <td>{{ $order['status_name'] }}</td>
                                    <td>{{ $order['created_at'] }}</td>
                                    <td>￥{{ $order['price'] }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                        <tfoot>
                        <td colspan="10" class="text-center">
                            {!! $orderNav !!}
                        </td>
                        </tfoot>
                    </table>
                </div>
                <div class="col-sm-12 table-responsive tables">
                    <p class="title-table">商品总计</p>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>商品编号</td>
                            <td>商品名称</td>
                            <td>平均单价</td>
                            <td>商品数量</td>
                            <td>商品支出金额</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($goods as $good )
                            <tr>
                                <td>{{ $good['id'] }}</td>
                                <td>{{ $good['name'] }}</td>
                                <td>{{ $good['num'] ? round($good['price'] / $good['num'],2):0 }}</td>
                                <td>{{ $good['num'] }}</td>
                                <td>{{ $good['price'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <td colspan="5" class="text-center">
                            {!! $goodsNav !!}
                        </td>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="order_page_num" value="{{ $orderCurrent or 1 }}"/>
                <input type="hidden" name="goods_page_num" value="{{ $goodsCurrent or 1 }}"/>
            </form>
            <div class="col-sm-12 table-responsive tables">
                <p class="title-table">订单总计</p>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td>订单数</td>
                        <td>总金额</td>
                        <td>在线支付订单数</td>
                        <td>在线支付订单总金额</td>
                        <td>货到付款订单数</td>
                        <td>货到付款总金额</td>
                        <td>{{ (request()->input('obj_type')==3 && auth()->user()->type != cons('user.type.retailer')) || auth()->user()->type == cons('user.type.retailer')?'货到付款实付金额':'货到付款实收金额' }}</td>
                        <td>{{ (request()->input('obj_type')==3 && auth()->user()->type != cons('user.type.retailer')) || auth()->user()->type == cons('user.type.retailer')?'货到付款未付金额':'货到付款未收金额' }}</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $otherStat['totalNum'] }}</td>
                        <td>{{ $otherStat['totalAmount'] }}</td>
                        <td>{{ $otherStat['onlineNum'] }}</td>
                        <td>{{ $otherStat['onlineAmount'] }}</td>
                        <td>{{ $otherStat['codNum'] }}</td>
                        <td>{{ $otherStat['codAmount'] }}</td>
                        <td>{{ $otherStat['codReceiveAmount'] }}</td>
                        <td>{{ $otherStat['codAmount'] - $otherStat['codReceiveAmount'] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        statisticsFunc();
        formSubmitByGet();
    </script>
@stop