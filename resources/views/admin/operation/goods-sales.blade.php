@extends('admin.master')

@section('subtitle' , '商品销售详情')

@include('includes.timepicker')
@include('includes.goods-sales-map')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/operation-data/sales-rank') }}">销售排行</a>
        <a href="javascript:" class="active">商品销售详情</a>
    </div>
    <div class="content-wrap">
        <div class="row">
            <div class="col-xs-12 sales-details">
                <form class="form-horizontal" action="{{ url('admin/operation-data/goods-sales') }}" method="get"
                      autocomplete="off">
                    <input type="text" name="begin_day" class="enter-control date datetimepicker"
                           data-format="YYYY-MM-DD"
                           value="{{ $beginDay }}">
                    <label class="control-label">-</label>
                    <input type="text" name="end_day" class="enter-control date datetimepicker" data-format="YYYY-MM-DD"
                           value="{{ $endDay}}">
                    <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                            class="address-province control"></select>
                    <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                            class="address-city control"></select>
                    <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                            class="address-district useless-control hide control"> </select>
                    <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                            class="address-street useless-control hide control"> </select>
                    <input type="text" class="enter-control product-name" name="q"
                           value="{{ $data['q'] or '' }}" placeholder="商品条形码/商品名称">
                    <select class="control" name="user_type">
                        <option value="">店铺类型</option>
                        @foreach(cons()->valueLang('user.type') as $type => $name)
                            @if($type != cons('user.type.retailer'))
                                <option value="{{ $type }}" {{ isset($data['user_type']) && $data['user_type'] == $type ? 'selected' : '' }}>{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <input type="submit" class="btn btn-blue control search-by-get" value="查询"/>
                    <a href="{{ url('admin/operation-data/goods-sales-export?' . http_build_query(array_except($data, 'page'))) }}"
                       class="btn btn-border-blue control">导出</a>
                </form>

                <table class="table public-table table-bordered goods-list">
                    <tr>
                        <th>商品ID</th>
                        <th>商品条形码</th>
                        <th>商品名称</th>
                        <th>所属分类</th>
                        <th>店铺名</th>
                        <th>销售量</th>
                        <th>销售金额(元)</th>
                        <th>操作</th>
                    </tr>
                    @foreach($orderGoods as $item)
                        <tr>
                            <td> {{ $goodsId = $item->goods_id }}</td>
                            <td>{{ $goods[$goodsId]->bar_code }}</td>
                            <td><a class="commodity-name" title="{{$goods[$goodsId]->name}}">{{ str_limit($goods[$goodsId]->name,20) }}</a></td>
                            <td>{{ $goods[$goodsId]->category_name }}</td>
                            <td>{{ $goods[$goodsId]->shop_name . '(' . cons()->valueLang('user.type', $goods[$goodsId]->user_type ) . ')' }}</td>
                            <td>{{ $item->count }}</td>
                            <td>{{ number_format($item->amount, 2) }}</td>
                            <td>
                                <a class="operating" href="javascript:" data-target="#goodsSalesModal"
                                   data-toggle="modal"
                                   data-id="{{ $goodsId }}"
                                   data-begin-day="{{ $beginDay }}"
                                   data-end-day="{{ $endDay }}"
                                   data-province-id = "{{ $data['province_id'] or 0 }}"
                                   data-city-id = "{{ $data['city_id'] or 0 }}"
                                >
                                    <i class="iconfont icon-qushitu"></i>分析图势</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="text-right">
        <ul class="pagination">
        </ul>
    </div>
@stop

@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        tablePage($('.goods-list'), $('.pagination'), 15);
        formSubmitByGet();
    </script>
@stop