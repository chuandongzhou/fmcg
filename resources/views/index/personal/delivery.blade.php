@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '个人中心-配送历史查询')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> &rarr; <a href="{{ url('personal/shop') }}">配送历史</a>
@stop
@section('css')
    @parent
    <style>
        input {
            height: 30px;
            margin-right: 10px;
            vertical-align: middle;
        }

        select {
            height: 30px;
            margin-right: 10px;
            vertical-align: middle;
        }

        button {
            height: 30px;

            color: #555555;
        }

        .control-search {
            padding: 10px 30px;
        }

        th {
            text-align: center
        }
    </style>
@stop
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 collect">
            <div class="row">
                <div class="col-sm-12 control-search">
                    <form action="{{ url('personal/delivery') }}" method="get" autocomplete="off">
                        时间段
                        <input class="enter datetimepicker" name="start_at"
                               placeholder="开始时间" type="text"
                               value="{{ $search['start_at'] or '' }}">至
                        <input class="enter datetimepicker" name="end_at"
                               placeholder="结束时间" type="text"
                               value="{{ $search['end_at'] or '' }}">


                        <select name="delivery_man_id">
                            <option value="">所有配送人员</option>
                            @foreach($deliveryMen as $man)
                                <option value="{{ $man->id  }}"{{ $man->id==$search['delivery_man_id'] ? 'selected' : ''}}>{{ $man->name }}</option>
                            @endforeach
                        </select>
                        <button class=" btn search search-by-get">搜索</button>
                    </form>
                </div>
            </div>
            <div class="col-sm-12 table-responsive tables">
                <table class="table-bordered table">
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

                            <td>{{ $delivery->deliveryMan?$delivery->deliveryMan->name:''  }}</td>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ $delivery->user->shop->name }}</td>
                            <td>{{ $delivery->pay_status==1?'已支付':'未支付' }}</td>
                            <td>
                                @if($delivery->pay_status==1)
                                    {{ isset($delivery->systemTradeInfo)?cons()->valueLang('trade.pay_type', $delivery->systemTradeInfo->pay_type):'' }}
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
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop

