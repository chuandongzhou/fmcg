@extends('index.menu-master')
@include('includes.timepicker')
@section('right')
    <div class="row my-goods order-report">
         <div class="col-sm-12 content">
        {{--<div class="col-sm-12 title">--}}
            {{--<a href="#" class="active">待付款2</a>--}}
            {{--<a href="#">待收货2</a>--}}
        {{--</div>--}}
        <form action="{{ url('order/statistics') }}" method="get">
            <div class="col-sm-12 enter-item">
                时间段
                <input class="enter datetimepicker" name="start_at" placeholder="{{ empty($search['start_at'])? '开始时间' : $search['start_at']}}" type="text" value="{{ $search['start_at'] or '' }}">至
                <input class="enter datetimepicker" name="end_at" placeholder="{{ empty($search['end_at']) ? '开始时间' : $search['end_at']}}" type="text" value="{{ $search['end_at'] or '' }}">
                <select class="enter" name="pay_type">
                    <option value="">全部方式</option>
                    @foreach($pay_type as $key => $value)
                        <option value="{{ $key }}" {{ ($key==(isset($search['pay_type']) ? $search['pay_type'] : '')) ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @if(auth()->user()->type != cons('user.type.wholesaler'))
                    <select class="enter" name="obj_type">
                        <option value="">全部订单对象</option>

                        @foreach($obj_type as $key => $value)
                            <option value="{{ $key }}" {{ ($key==(isset($search['obj_type']) ? $search['obj_type'] : '')) ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                @endif
                <button class="btn" type="submit" >统计</button>
                <a href="#" class="btn btn-primary">统计导出</a>
            </div>

                <div class="col-sm-12 enter-item">
                    <div class="check-item"><span class="span-checkbox"><i class="fa {{$search['checkbox_flag']==1 ? 'fa-check' : ''}}"></i></span>
                        <input class="inp-checkbox" type="checkbox" {{$search['checkbox_flag']==1 ? 'checked' : ''}}>显示商品</div>
                    <input type="hidden" class="checkbox-flag" value="{{ $search['checkbox_flag'] }}" name="checkbox_flag" />
                    <input type="text" class="enter" name="goods_name" placeholder="{{ empty($search['goods_name']) ? '商品名称' : $search['goods_name']}}">
                    <input type="text" class="enter" name="seller_name" placeholder="{{ empty($search['seller_name']) ? '商家名称' : $search['seller_name']}}">
                    @if(Auth()->user()->type == cons('user.type.retailer'))
                        <p class="item address" >商家地址 :
                            <select data-id="{{ $search['province_id'] or 0 }}" class="enter address-province" name="province_id"></select>
                            <select data-id="{{ $search['city_id'] or 0 }}" class="enter address-city" name="city_id"></select>
                            <select data-id="{{ $search['district_id'] or 0 }}" class="enter address-district" name="district_id"></select>
                            <input type="hidden" class="enter address-street" />
                        </p>
                    @endif
                </div>
        </form>
        <div class="col-sm-12 table-responsive tables">
            <table class="table-bordered table">
                <thead>
                    <tr>
                        <td>订单号</td>
                        <td>收件人</td>
                        <td>支付方式</td>
                        <td>订单状态</td>
                        <td>时间</td>
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
                    @foreach($statistics['data'] as $order)
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
                                        <td>{{ $order['shipping_address']['consigner'] }}</td>
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
                                <td>{{ $order['shipping_address']['consigner'] }}</td>
                                <td>{{ $order['payment_type'] }}</td>
                                <td>{{ $order['status_name'] }}</td>
                                <td>{{ $order['created_at'] }}</td>
                                <td>￥{{ $order['price'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
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
                    @foreach($otherStat['goods'] as $key => $good )
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $good['name'] }}</td>
                        <td>{{ $good['price']/$good['num'] }}</td>
                        <td>{{ $good['num'] }}</td>
                        <td>{{ $good['price'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
@stop
@section('js-lib')
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
    <script>
        $('.span-checkbox').click(function(){
            var isCheck=$(this).siblings('.inp-checkbox').is(':checked');
            if(isCheck==false){
                $(this).children('.fa').addClass('fa-check');
                $(this).siblings('.inp-checkbox').prop('checked',true);
                $('.checkbox-flag').val(1);
            }else{
                $(this).children('.fa').removeClass('fa-check');
                $(this).siblings('.inp-checkbox').prop('checked',false);
                $('.checkbox-flag').val(0);
            }
            //提交表单
            $('form').submit();
        })

    </script>
@stop