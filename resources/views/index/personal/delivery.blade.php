@extends('index.menu-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-配送历史查询')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> > <span class="second-level"> 配送历史</span>
@stop

@section('right')
    <div class="row delivery">
        <div class="col-sm-12 control-search">
            <form action="" method="get" autocomplete="off">
                <input class="enter control datetimepicker" name="start_at"
                       placeholder="开始时间" type="text" data-format="YYYY-MM-DD" data-max-date="true"
                       value="{{ $search['start_at'] or '' }}">至
                <input class="enter control datetimepicker" name="end_at"
                       placeholder="结束时间" type="text" data-format="YYYY-MM-DD" data-max-date="true"
                       value="{{ $search['end_at'] or '' }}">
                <select name="delivery_man_id" class="control ajax-select">
                    <option value="">所有配送人员</option>
                    @foreach($deliveryMen as $man)
                        <option value="{{ $man->id  }}"{{ isset($search['delivery_man_id'])&&$man->id==$search['delivery_man_id'] ? 'selected' : ''}}>{{ $man->name }}</option>
                    @endforeach
                </select>
                <button type="button" class=" btn btn-blue-lighter search control search-by-get">搜索</button>
                <button type="button" class="btn btn-default control statistical">汇总统计</button>
            </form>
        </div>
        <div class="col-sm-12 table-responsive table-wrap">
            <table class="table-bordered table table-center public-table">
                <thead>
                <tr align="center">
                    <th>配送人员</th>
                    <th>订单号</th>
                    <th>店家名称</th>
                    <th>支付状态</th>
                    <th>支付方式</th>
                    <th>收货地址</th>
                    <th>完成配送时间</th>
                </tr>
                </thead>
                <tbody>

                @foreach($deliveries as $delivery)
                    <tr align="center">
                        @if($delivery->deliveryMan)
                            <td>{!! implode("|",array_column($delivery->deliveryMan->toArray(), 'name')) !!} </td>
                        @else
                            <td></td>
                        @endif

                        {{--<td>{{ $delivery->deliveryMan?$delivery->deliveryMan->name:''  }}</td>--}}
                        <td>{{ $delivery->id }}</td>
                        <td>{{ $delivery->user_shop_name }}</td>
                        <td>{{ $delivery->pay_status==1?'已支付':'未支付' }}</td>
                        <td>
                            @if($delivery->pay_status==1)
                                @if($delivery->pay_type==cons('pay_type.cod'))
                                    {{ $delivery->pay_way_lang  }}
                                @else
                                    {{ isset($delivery->systemTradeInfo)?cons()->valueLang('trade.pay_type', $delivery->systemTradeInfo->pay_type):'现金' }}
                                @endif


                            @else
                                {{ $delivery->pay_type==1?'线上支付':'线下支付' }}
                            @endif
                        </td>
                        <td>{{  $delivery->shippingAddress&&$delivery->shippingAddress->address?$delivery->shippingAddress->address->address_name:'' }}</td>
                        <td>{{ $delivery->delivery_finished_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                {!! $deliveries->appends(array_filter($search))->render() !!}
            </div>
        </div>

    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        $('.statistical').click(function () {
            checksubmit(site.url('personal/delivery-statistical'));
        });
        $('.search-by-get').click(function () {
            checksubmit(site.url('personal/delivery'));
        });
        function checksubmit(url) {
            $("form").attr('action', url);
            $('form').submit();
        }
    </script>
@stop

