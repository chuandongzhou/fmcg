@extends('admin.master')

@section('subtitle' , '销售排行')

@include('includes.timepicker')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="javascript:" class="active">销售排行</a>
        <a href="{{ url('admin/operation-data/goods-sales') }}">商品销售详情</a>
    </div>
    <div class="content-wrap sales-details">
        <form class="form-horizontal" action="{{ url('admin/operation-data/sales-rank') }}" method="get"
              autocomplete="off">
            <a href="{{ url('admin/operation-data/sales-rank?t=today') }}"
               class="time-format {{ array_get($data, 't') == 'today' ? 'active' : '' }}">今天</a>
            <a href="{{ url('admin/operation-data/sales-rank?t=yesterday') }}"
               class="time-format {{ array_get($data, 't') == 'yesterday' ? 'active' : '' }}">昨天</a>
            <a href="{{ url('admin/operation-data/sales-rank?t=week') }}"
               class="time-format {{ array_get($data, 't') == 'week' ? 'active' : '' }}">本周</a>
            <a href="{{ url('admin/operation-data/sales-rank?t=month') }}"
               class="time-format {{ array_get($data, 't') == 'month' ? 'active' : '' }}">本月</a>
            <input type="text" name="begin_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $beginDay }}">
            <label class="control-label">-</label>
            <input type="text" name="end_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                   value="{{ $endDay }}">

            <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                    class="address-province control"></select>
            <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                    class="address-city control"></select>
            <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                    class="address-district useless-control hide control"> </select>
            <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                    class="address-street useless-control hide control"> </select>

            <select name="type" class="control">
                <option value="">用户类型</option>
                @foreach(cons()->valueLang('user.type') as $key => $item)
                    @if($key != cons('user.type.retailer'))
                        <option value="{{ $key }}" {{ array_get($data, 'type') == $key ? 'selected' : '' }}>{{ $item }}</option>
                    @endif
                @endforeach
            </select>

            <input type="submit" class="btn btn-blue control search-by-get" value="查询"/>
            <a href="{{ url('admin/operation-data/sales-rank-export?' . http_build_query($data)) }}"
               class="btn btn-border-blue control export">导出</a>
        </form>
        <div class="tab-content">
            <div class="row">
                <div class="col-xs-6">
                    <div class="text-center title-name">前十商品销售金额排位</div>
                    <table class="table public-table table-bordered">
                        <tr>
                            <th>排序</th>
                            <th>商品名称</th>
                            <th>所属分类</th>
                            <th>销售金额(元)</th>
                        </tr>
                        @foreach($orderGoods as $key =>$item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <a class="commodity-name">{{ str_limit($goods[$item->goods_id]->name , 10) }}</a>
                                </td>
                                <td>{{ $goods[$item->goods_id]->category_name }}</td>
                                <td>{{ $item->amount }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-xs-6">
                    <div class="text-center title-name">前十商铺销售金额排位</div>
                    <table class="table public-table table-bordered">
                        <tr>
                            <th>排序</th>
                            <th>店铺名称</th>
                            <th>店铺类型</th>
                            <th>销售金额(元)</th>
                        </tr>
                        @foreach($shopAmount as $key =>$item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a class="commodity-name">{{ $shops[$item->shop_id]->name }}</a></td>
                                <td>{{ cons()->valueLang('user.type',$shops[$item->shop_id]->user_type) }}</td>
                                <td>{{ $item->amount }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
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
        formSubmitByGet();
    </script>
@stop