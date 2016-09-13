@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '个人中心-配送统计')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> &rarr; <a href="{{ url('personal/delivery') }}">配送历史</a>&rarr;配送统计
@stop
@section('css')
    @parent
    <style>
        tr {
            text-align: center;
        }

        .notice-bar {
            margin-bottom: 10px;
        }

        .back {
            margin-left: 20px
        }
    </style>
@stop
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 enter-item notice-bar">
            <a class="btn btn-default col-xs-1"
               href="{{ url('personal/delivery-report?'.  http_build_query($search)) }}">下载打印</a>
            <a class="btn btn-default back" href="javascript:history.back()"><i class="fa fa-reply"></i> 返回</a>
        </div>
        <div class="col-sm-12 table-responsive tables">
            <table class="table-bordered table">
                @foreach($data['deliveryMan'] as $key => $deliveryMan)
                    <tr>
                        <td>配送人员</td>
                        <td>{{ $key }}</td>
                        <td>时间</td>
                        <td>
                            @if(!empty($search['start_at']) && empty($search['end_at']))
                                {{  $search['start_at'] }}至今
                            @else
                                {{  $search['start_at'] }}至{{ $search['end_at'] }}
                            @endif
                        </td>
                        <td>配送单数</td>
                        <td>{{ $deliveryMan['orderNum'] }}</td>
                    </tr>
                    <tr>
                        <td>配送订单总金额</td>
                        <td>现金</td>
                        <td>POS机</td>
                        <td>易宝</td>
                        <td>支付宝</td>
                        <td>平台余额</td>
                    </tr>
                    <tr>
                        <td>{{ $deliveryMan['totalPrice'] }}</td>
                        <td>{{ array_key_exists(0,$deliveryMan['price'])?$deliveryMan['price'][0]:'0.00' }}</td>
                        <td>{{ array_key_exists(cons('trade.pay_type.pos'),$deliveryMan['price'])?$deliveryMan['price'][cons('trade.pay_type.pos')]:'0.00' }}</td>
                        <td>{{ @bcadd($deliveryMan['price'][cons('trade.pay_type.yeepay')],$deliveryMan['price'][cons('trade.pay_type.yeepay_wap')],2) }}</td>
                        <td>{{ @bcadd($deliveryMan['price'][cons('trade.pay_type.alipay_pc')],$deliveryMan['price'][cons('trade.pay_type.alipay')],2) }}</td>
                        <td>{{ array_key_exists(cons('trade.pay_type.balancepay'),$deliveryMan['price'])?$deliveryMan['price'][cons('trade.pay_type.balancepay')]:'0.00' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6">商品列表</td>
                </tr>
                <tr>
                    <td colspan="2">商品名称</td>
                    <td colspan="2">商品数量</td>
                    <td colspan="2">金额</td>
                </tr>
                @foreach($data['goods'] as $key => $goods)
                    @foreach($goods as $k => $detail)
                        <tr>
                            @if(array_keys($goods)[0]==$k)
                                <td colspan="2" rowspan="{{ count($goods) }}">{{ $key }}</td>
                            @endif
                            <td colspan="2">{{ $detail['num'] }}{{ cons()->valueLang('goods.pieces', $k) }}</td>
                            <td colspan="2">{{ $detail['price'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop

