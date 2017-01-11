@extends('admin.master')

@include('includes.timepicker')

@include('includes.salesman-customer-map')

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
            <a href="{{ url("admin/promoter/export?start_day={$startDay}&end_day={$endDay}") }}" class="btn btn-border-blue control">导出</a>
        </form>
        <table class="table public-table table-bordered">
            <tr>
                <th>推广员</th>
                <th>推广码</th>
                <th>注册终端数</th>
                <th>注册批发商数</th>
                <th>总注册数量</th>
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
                    <td>{{ $promoter->retailerShopRegisterCount + $promoter->wholesalerShopRegisterCount }}</td>
                    <td>{{ $promoter->submitOrdersUsersCount }}</td>
                    <td>{{ $promoter->finishedOrdersUserCount }}</td>
                    <td>{{ $promoter->submitOrdersCount }}</td>
                    <td>{{ $promoter->finishedOrdersCount }}</td>
                    <td>{{ number_format( $promoter->submitOrdersAmount , 2) }}</td>
                    <td>{{ number_format( $promoter->finishedOrdersAmount , 2) }}</td>
                    <td>
                        <a class="btn shop-coordinate" href="javascript:;" data-target="#customerAddressMapModal"
                           data-toggle="modal"
                           data-coordinate= {{ $promoter->shopsCoordinate }}>
                            <i class="iconfont icon-renkoufenbu"></i> 客户分布
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mUrGqwp43ceCzW41YeqmwWUG"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('.shop-coordinate').each(function () {
            var $this = $(this), coordinate = $this.data('coordinate'), mapData = [];;
            for (var i in coordinate) {
                var obj = coordinate[i], data = [];
                data['lng'] = obj.lng;
                data['lat'] = obj.lat;
                data['number'] = '客户 ' + obj.number;
                data['name'] = obj.name;
                data['href'] = obj.href;
                mapData.push(data);
            }
            $this.data('shopCoordinate', mapData);
        });


        //        var customerMapData = function () {
        //            var mapData = [];
        //            $('.salesman-customer-table  .map-data').each(function () {
        //                var obj = $(this), data = [];
        //                data['lng'] = obj.data('lng');
        //                data['lat'] = obj.data('lat');
        //                data['number'] = '客户 ' + obj.data('number');
        //                data['name'] = obj.data('name');
        //                data['href'] = site.url('business/salesman-customer/' + obj.data('id'));
        //                mapData.push(data);
        //            });
        //            return mapData;
        //        };
    </script>
@stop