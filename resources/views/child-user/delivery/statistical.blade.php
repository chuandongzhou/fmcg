@extends('child-user.manage-master')
@include('includes.timepicker')

@section('subtitle', '个人中心-配送统计')
@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/info') }}">个人中心</a> > <a href="{{ url('child-user/delivery') }}">配送历史</a>> <span
                            class="second-level">配送统计</span>
                </div>
            </div>

            <div class="row delivery-statistics">
                <div class="col-sm-12 operation">
                    <a href="{{  url('child-user/delivery?'.  http_build_query(array_except($search , ['num']))) }}"
                       class="btn btn-border-blue"><i class="iconfont icon-fanhui"></i>返回</a>
                    <a href="{{ url('child-user/delivery/report?'.  http_build_query($search)) }}"
                       class="btn btn-border-blue">下载打印</a>
                </div>
                <div class="col-sm-12 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><b>配送人员汇总</b></h3>
                        </div>
                        <div class="panel-container clearfix">
                            <table class="table table-bordered table-center public-table">
                                <thead>
                                <tr>
                                    <th>配送人员</th>
                                    <th>时间</th>
                                    <th>配送单数</th>
                                    <th>配送订单总金额</th>
                                    <th>现金</th>
                                    <th>POS机</th>
                                    <th>易宝</th>
                                    <th>支付宝</th>
                                    <th>平台余额</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['deliveryMan'] as $key => $deliveryMan)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td> {{  !empty($search['start_at'])?$search['start_at']: $deliveryMan['first_time'] }}
                                            至{{  !empty($search['end_at'])?$search['end_at']:'今' }}</td>
                                        <td>{{ $deliveryMan['orderNum'] }}</td>
                                        <td>{{ $deliveryMan['totalPrice'] }}</td>
                                        <td>{{ array_key_exists(0,$deliveryMan['price'])?$deliveryMan['price'][0]:'0.00' }}</td>
                                        <td>{{ array_key_exists(cons('trade.pay_type.pos'),$deliveryMan['price'])?$deliveryMan['price'][cons('trade.pay_type.pos')]:'0.00' }}</td>
                                        <td>{{ @bcadd($deliveryMan['price'][cons('trade.pay_type.yeepay')],$deliveryMan['price'][cons('trade.pay_type.yeepay_wap')],2) }}</td>
                                        <td>{{ @bcadd($deliveryMan['price'][cons('trade.pay_type.alipay_pc')],$deliveryMan['price'][cons('trade.pay_type.alipay')],2) }}</td>
                                        <td>{{ array_key_exists(cons('trade.pay_type.balancepay'),$deliveryMan['price'])?$deliveryMan['price'][cons('trade.pay_type.balancepay')]:'0.00' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default commodity-summary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><b>商品汇总</b></h3>
                        </div>
                        <div class="panel-container clearfix">

                            @if(!empty($search['delivery_man_id']) &&  !empty($deliveryNum))
                                <div class="text-center control-panel">
                                    <select class="control num">
                                        <option value="{{ url('child-user/delivery-statistical?'.  http_build_query(array_except($search , ['num']))) }}">
                                            全部
                                        </option>
                                        @foreach($deliveryNum as $num)
                                            <option {{ !empty($search['num'])&&$num==$search['num']?'selected':'' }} value="{{ url('child-user/delivery-statistical?'.  http_build_query(array_except($search , ['num'])).'&num='.$num) }}"> {{ $num.'人配送' }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn control btn-border-blue search-by-num">汇总统计</button>
                                </div>
                            @endif

                            @foreach($data['goods'] as $deliveryNum => $allgoods)
                                <table class="table table-bordered table-center public-table">
                                    <thead>
                                    @if(!empty($search['delivery_man_id']))
                                        <tr>
                                            <td colspan="4" class="delivery-number">{{ $deliveryNum.'人配送' }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>商品名称</th>
                                        <th>购买角色</th>
                                        <th>数量</th>
                                        <th>金额</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($allgoods as $goodsName => $goods)
                                        @foreach($goods as $userType => $detail)
                                            @foreach($detail as $goodsPieces=>$goodsDetail)
                                                <tr>
                                                    @if(array_keys($goods)[0]==$userType && array_keys($detail)[0]==$goodsPieces)
                                                        <td rowspan="{{ count(array_flatten($goods))/2 }}">{{ $goodsName  }}</td>
                                                    @endif
                                                    @if(array_keys($detail)[0]==$goodsPieces)
                                                        <td rowspan="{{ count($detail) }}">{{ cons()->valueLang('user.type', cons('user.type.'.$userType)) }}</td>
                                                    @endif
                                                    <td>{{ $goodsDetail['num'].cons()->valueLang('goods.pieces', $goodsPieces) }}</td>
                                                    <td>{{ $goodsDetail['price'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        $('.search-by-num').click(function () {
            var url = $('.num').val();
            location.href = url;
        });
    </script>
@stop

