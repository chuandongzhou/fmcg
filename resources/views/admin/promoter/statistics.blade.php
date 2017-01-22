@extends('admin.master')

@include('includes.timepicker')

@include('admin.promoter.customer-map')

@section('subtitle' , '推广统计')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/promoter') }}" class="{{ path_active('admin/promoter') }}">销售推广</a>
        <a href="{{ url('admin/promoter/statistics') }}" class="{{ path_active('admin/promoter/statistics') }}">推广统计</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal" action="{{ url('admin/promoter/statistics') }}" method="get"
              autocomplete="off">
            <input type="text" class="enter-control datetimepicker" name="start_day" placeholder="开始时间"
                   data-format="YYYY-MM-DD"
                   value="{{ $startDay }}">
            至
            <input type="text" class="enter-control datetimepicker" name="end_day" placeholder="结束时间"
                   data-format="YYYY-MM-DD"
                   value="{{ $endDay }}">
            <input type="submit" class="btn btn-blue control" value="查询"/>
            <a href="{{ url("admin/promoter/export?start_day={$startDay}&end_day={$endDay}") }}"
               class="btn btn-border-blue control">导出</a>
        </form>
        <div class="table-responsive table-scroll">
            <table class="table public-table table-bordered">
                <tr>
                    <th>推广员</th>
                    <th>推广码</th>
                    <th>注册终端数</th>
                    <th>注册批发数</th>
                    <th>注册供应数</th>
                    <th>总注册数量</th>
                    <th>活跃终端数</th>
                    <th>活跃批发数</th>
                    <th>活跃供应数</th>
                    <th>下单客户数</th>
                    <th>成交客户数</th>
                    <th>订单数</th>
                    <th>成交订单数</th>
                    <th>下单金额</th>
                    <th>成交金额</th>
                    <th>操作</th>
                </tr>
                @foreach($promoters as $promoter)
                    <tr>
                        <td>{{ $promoter->name }}</td>
                        <td>{{ $promoter->spreading_code }}</td>
                        <td>{{ $promoter->retailerShopRegisterCount }}</td>
                        <td>{{ $promoter->wholesalerShopRegisterCount }}</td>
                        <td>{{ $promoter->supplierShopRegisterCount }}</td>
                        <td>{{ $promoter->retailerShopRegisterCount + $promoter->wholesalerShopRegisterCount + $promoter->supplierShopRegisterCount }}</td>
                        <td>{{ $promoter->retailerUserActiveCount }}</td>
                        <td>{{ $promoter->wholesalerUserActiveCount }}</td>
                        <td>{{ $promoter->supplierUserActiveCount }}</td>
                        <td>{{ $promoter->submitOrdersUsersCount }}</td>
                        <td>{{ $promoter->finishedOrdersUserCount }}</td>
                        <td>{{ $promoter->submitOrdersCount }}</td>
                        <td>{{ $promoter->finishedOrdersCount }}</td>
                        <td>{{ number_format( $promoter->submitOrdersAmount , 2) }}</td>
                        <td>{{ number_format( $promoter->finishedOrdersAmount , 2) }}</td>
                        <td>
                            <a class="edit" href="javascript:;" data-target="#customerMapModal"
                               data-toggle="modal"
                               data-shops={{ $promoter->shops }}
                                       data-name= {{ $promoter->name }}
                               data-spreading-code={{ $promoter->spreading_code }}
                                    data-finish-amount= {{ $promoter->finishedOrdersAmount }}
                               data-user-count= {{ $promoter->finishedOrdersUserCount }}
                            >
                                <i class="iconfont icon-renkoufenbu"></i> 客户分布
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop
