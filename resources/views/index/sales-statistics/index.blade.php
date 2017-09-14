@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '销售统计')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('order-sell') }}">销售统计</a> >
                    <span class="second-level"> 统计</span>
                </div>
            </div>
            <div class="row wholesalers-management margin-clear">
                <form class="form" method="get" action="{{ url('sales-statistics') }}" autocomplete="off">
                    <div class="col-sm-12 pay-detail search-options">
                        <input type="text" class="datetimepicker control" placeholder="开始时间" name="start_at"
                               data-format="YYYY-MM-DD" value="{{ $startAt }}"/>　至　
                        <input type="text" class="datetimepicker control" id="end-time" placeholder="结束时间" name="end_at"
                               data-format="YYYY-MM-DD" value="{{ $endAt }}"/>

                        <select name="goods_type" class="ajax-select control">
                            <option value="">全部类型</option>
                            <option value="0" {{ $goodsType === '0' ? 'selected' : '' }}>商品销售统计</option>
                            @if($user->type != cons('user.type.maker'))
                                <option value="1" {{ $goodsType == 1 ? 'selected' : '' }}>陈列统计</option>
                                <option value="2" {{ $goodsType == 2 ? 'selected' : '' }}>赠品统计</option>
                            @endif
                        </select>
                        <select name="salesman" class="ajax-select control">
                            <option value="">请选择业务员</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ $salesman->id == $salesmanId ? 'selected' : ''  }}>{{ $salesman->name }}</option>
                            @endforeach
                        </select>

                        <select name="order_type" class="ajax-select control">
                            <option value="">订单类型</option>
                            <option value="0" {{ $orderType === '0' ? 'selected' : '' }}>自主订单</option>
                            <option value="1" {{ $orderType == 1 ? 'selected' : '' }}>业务订单</option>
                        </select>

                        <select name="search_type" class="ajax-select control">
                            <option value="goods">商品名称</option>
                            <option value="shops" {{ $searchType == 'shops' ? 'selected' : '' }}>商户名称</option>
                        </select>

                        <input type="text" class="control" placeholder="请输入" name="value" value="{{ $value }}"/>


                        <button type="submit" data-action="{{ url('sales-statistics') }}"
                                class="btn btn-primary search-by-get">搜索
                        </button>
                        <button type="submit" data-action="{{ url('sales-statistics/export') }}"
                                class="btn btn-default search-by-get">导出
                        </button>
                    </div>
                </form>

                <div class="col-sm-12 padding-clear">
                    <ul id="myTab" class="nav nav-tabs notice-bar padding-clear">
                        @if(is_null($goodsType)|| $goodsType === '0')
                            <li class="{{ is_null($goodsType)|| $goodsType === '0' ? 'active' : '' }}">
                                <a href="#table1" data-toggle="tab">商品销售统计</a>
                            </li>
                        @endif
                        @if($user->type != cons('user.type.maker'))
                            @if(is_null($goodsType)|| $goodsType === '1')
                                <li class="{{ $goodsType === '1'? 'active' : '' }}">
                                    <a href="#table2" data-toggle="tab">陈列统计</a></li>
                            @endif
                            @if(is_null($goodsType)|| $goodsType === '2')
                                <li class="{{ $goodsType === '2'? 'active' : '' }}">
                                    <a href="#table3" data-toggle="tab">赠品统计</a></li>
                            @endif
                        @endif
                    </ul>
                    <div id="myTabContent" class="tab-content ">
                        @if(is_null($goodsType)|| $goodsType === '0')
                            <div class="tab-pane fade active in padding-clear tables" id="table1">
                                <table class="table table-bordered table-center table-middle public-table">
                                    <thead>
                                    <tr>
                                        <th>商品ID</th>
                                        <th>商品条形码</th>
                                        <th>商品名称</th>
                                        <th>销售数量</th>
                                        <th>销售金额</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orderGoods as $goods)
                                        <tr>
                                            <td>{{ $goods['id'] }}</td>
                                            <td>{{ $goods['barcode'] }}</td>
                                            <td>{{ $goods['name'] }}</td>
                                            <td>
                                                @foreach($goods['num'] as $pieces=>$num)
                                                    {{ $num . cons()->valueLang('goods.pieces', $pieces) }}
                                                @endforeach
                                            </td>
                                            <td>{{ $goods['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    @if($goodsType === '1')
                                        <tfoot>
                                        <tr>
                                            <th colspan="5">陈列现金统计：{{ $displayFee }}元</th>
                                        </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        @endif
                        @if($user->type != cons('user.type.maker'))
                            @if(is_null($goodsType)|| $goodsType === '1')
                                <div class="tab-pane fade {{ $goodsType === '1' ? 'active' : '' }} in padding-clear tables" id="table2">
                                    <table class="table table-bordered table-center table-middle public-table">
                                        <thead>
                                        <tr>
                                            <th>商品ID</th>
                                            <th>商品条形码</th>
                                            <th>商品名称</th>
                                            <th>销售数量</th>
                                            <th>销售金额</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($mortgageGoods as $goods)
                                            <tr>
                                                <td>{{ $goods['id'] }}</td>
                                                <td>{{ $goods['barcode'] }}</td>
                                                <td>{{ $goods['name'] }}</td>
                                                <td>
                                                    @foreach($goods['num'] as $pieces=>$num)
                                                        {{ $num . cons()->valueLang('goods.pieces', $pieces) }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $goods['amount'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5">陈列现金统计：{{ $displayFee }}元</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                            @if(is_null($goodsType)|| $goodsType === '2')
                                <div class="tab-pane fade in {{ $goodsType === '2' ? 'active' : '' }} padding-clear tables" id="table3">
                                    <table class="table table-bordered table-center table-middle public-table">
                                        <thead>
                                        <tr>
                                            <th>商品ID</th>
                                            <th>商品条形码</th>
                                            <th>商品名称</th>
                                            <th>销售数量</th>
                                            <th>销售金额</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($giftGoods as $goods)
                                            <tr>
                                                <td>{{ $goods['id'] }}</td>
                                                <td>{{ $goods['barcode'] }}</td>
                                                <td>{{ $goods['name'] }}</td>
                                                <td>
                                                    @foreach($goods['num'] as $pieces=>$num)
                                                        {{ $num . cons()->valueLang('goods.pieces', $pieces) }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $goods['amount'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('form [type="submit"]').on('click', function () {
            var obj = $(this);
            obj.closest('form').attr('action', obj.data('action'));
            return false;
        });
        $(function () {
            formSubmitByGet();
        })
    </script>
@stop