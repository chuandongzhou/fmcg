@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '订单统计')
@section('right')
    <div class="row my-goods order-report">
        <div class="col-sm-12 content">
            <form action="{{ url('order/statistics') }}" method="get" autocomplete="off">
                <div class="col-sm-12 enter-item">
                    时间段
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
                    <button id="submitBtn" class="btn search-by-get" type="submit">统计</button>
                    @unless(empty($statistics))
                        <a id="export" class="btn btn-primary">统计导出</a>
                    @endunless
                </div>
                <div class="col-sm-12 enter-item">

                    <div class="item">
                        <p class="check-item"><span class="span-checkbox"><i
                                        class="fa {{$search['checkbox_flag']==1 ? 'fa-check' : ''}}"></i></span>
                            <input class="inp-checkbox"
                                   type="checkbox" {{$search['checkbox_flag']==1 ? 'checked' : ''}}>显示商品</p>
                        <input type="hidden" class="checkbox-flag" value="{{ $search['checkbox_flag'] }}"
                               name="checkbox_flag"/>
                        <input type="text" class="enter" name="goods_name" placeholder="商品名称"
                               value="{{ $search['goods_name'] or '' }}">
                        <input type="text" class="enter" name="user_name" placeholder="{{ $showObjName }}"
                               value="{{ $search['user_name'] or '' }}">
                    </div>
                    @if(Auth()->user()->type == cons('user.type.retailer'))
                        <div class="item">商家地址 :
                            <select data-id="{{ $search['province_id'] or 0 }}" class="enter address-province"
                                    name="province_id"></select>
                            <select data-id="{{ $search['city_id'] or 0 }}" class="enter address-city"
                                    name="city_id"></select>
                            <select data-id="{{ $search['district_id'] or 0 }}" class="enter address-district"
                                    name="district_id"></select>
                            <input type="hidden" class="enter address-street"/>
                        </div>
                    @endif
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
                            @if($search['checkbox_flag'])
                                <td>商品编号</td>
                                <td>商品名称</td>
                                <td>商品单价</td>
                                <td>商品数量</td>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($statistics as $order)
                            @if($search['checkbox_flag'])
                                @foreach($order['goods'] as $key => $value)
                                    @if($key)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $value['id'] }}</td>
                                            <td>{{ $value['name'] }}</td>
                                            <td>￥{{ $value['pivot']['price'] }}</td>
                                            <td>{{ $value['pivot']['num'] }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $order['id'] }}</td>
                                            <td>
                                                @if(auth()->user()->type == cons('user.type.wholesaler')&&$objCurrentType<cons('user.type.wholesaler') ||auth()->user()->type == cons('user.type.supplier'))
                                                    {{ $order['user']['shop']['name'] }}
                                                @else
                                                    {{ $order['shop']['name'] }}
                                                @endif
                                            </td>
                                            <td>{{ $order['payment_type'] }}</td>
                                            <td>{{ $order['status_name'] }}</td>
                                            <td>{{ $order['created_at'] }}</td>
                                            <td>￥{{ $order['price'] }}</td>
                                            <td>{{ $value['id'] }}</td>
                                            <td>{{ $value['name'] }}</td>
                                            <td>￥{{ $value['pivot']['price'] }}</td>
                                            <td>{{ $value['pivot']['num'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $order['id'] }}</td>
                                    @if(auth()->user()->type == cons('user.type.wholesaler')&&$objCurrentType<cons('user.type.wholesaler') ||auth()->user()->type == cons('user.type.supplier'))
                                        {{ $order['user']['shop']['name'] }}
                                    @else
                                        {{ $order['shop']['name'] }}
                                    @endif
                                    <td>{{ $order['payment_type'] }}</td>
                                    <td>{{ $order['status_name'] }}</td>
                                    <td>{{ $order['created_at'] }}</td>
                                    <td>￥{{ $order['price'] }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    {!! $orderNav !!}
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
                                <td>{{ round($good['price']/$good['num'],2) }}</td>
                                <td>{{ $good['num'] }}</td>
                                <td>{{ $good['price'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $goodsNav !!}
                </div>
                <input type="hidden" name="order_page_num" value="{{ $orderCurrent or 1 }}"/>
                <input type="hidden" name="goods_page_num" value="{{ $goodsCurrent or 1 }}"/>
            </form>
            {{--{!! $obj->render()  !!}--}}
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
                        <td>货到付款实收金额</td>
                        <td>货到付款未收金额</td>
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