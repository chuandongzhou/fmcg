@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '个人中心-配送历史查询')
@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> > <span class="second-level"> 配送历史</span>
@stop

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/info') }}">个人中心</a> > <span class="second-level"> 配送历史</span>
                </div>
            </div>

            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form action="{{ url('personal/delivery/history') }}" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at"
                               placeholder="开始时间" type="text" data-format="YYYY-MM-DD" data-max-date="true"
                               value="{{ array_get($search, 'start_at') }}">至
                        <input class="enter control datetimepicker" name="end_at"
                               placeholder="结束时间" type="text" data-format="YYYY-MM-DD" data-max-date="true"
                               value="{{ array_get($search, 'end_at') }}">
                        <select name="delivery_man_id" class="control ajax-select">
                            <option value="">所有配送人员</option>
                            @foreach($deliveryMen as $man)
                                <option value="{{ $man->id  }}"{{ $man->id == array_get($search, 'delivery_man_id') ? 'selected' : ''}}>{{ $man->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" data-url="{{ url('personal/delivery/history') }}"
                                class=" btn btn-blue-lighter search control search-by-get">搜索
                        </button>
                        <button type="submit" data-url="{{ url('personal/delivery/statistical') }}"
                                class="btn btn-default control statistical  search-by-get">汇总统计
                        </button>
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
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('form').find('button[type="submit"]').on('click', function () {
            $(this).closest('form').attr('action', $(this).data('url'));
        });
        formSubmitByGet();
    </script>
@stop

