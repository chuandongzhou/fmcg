@extends('index.menu-master')
@include('includes.timepicker')

@section('subtitle', '个人中心-配送历史查询')
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 collect">
            <div class="row">
                <div class="col-sm-12 control-panel">
                    <form action="{{ url('personal/delivery') }}" method="get" autocomplete="off">
                        时间段
                        <input class="enter datetimepicker" name="start_at"
                               placeholder="{{ empty($search['start_at'])? '开始时间' : $search['start_at']}}" type="text"
                               value="{{ $search['start_at'] or '' }}">至
                        <input class="enter datetimepicker" name="end_at"
                               placeholder="{{ empty($search['end_at']) ? '结束时间' : $search['end_at']}}" type="text"
                               value="{{ $search['end_at'] or '' }}">


                        <select name="delivery_man_id">
                            <option>请选择</option>
                            @foreach($deliveryMen as $man)
                                <option value="{{ $man->id  }}"{{ $man->id==$search['delivery_man_id'] ? 'selected' : ''}}>{{ $man->name }}</option>
                            @endforeach
                        </select>
                        <button class=" btn btn-cancel search search-by-get">搜索</button>
                    </form>
                </div>
            </div>
            <div class="col-sm-12 table-responsive tables">
                <table class="table-bordered table">
                    <thead>
                    <tr>
                        <td>配送人员</td>
                        <td>订单号</td>
                        <td>店家名称</td>
                        <td>收货地址</td>
                        <td>完成配送时间</td>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>

                            <td>{{ $delivery->deliveryMan->name  }}</td>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ $delivery->shop->name }}</td>
                            <td>{{ $delivery->shippingAddress->address?$delivery->shippingAddress->address->area_name:'' }}</td>
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
