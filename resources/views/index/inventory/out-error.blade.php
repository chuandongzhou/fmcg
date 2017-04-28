@extends('index.menu-master')
@section('subtitle', '入库异常')
@section('top-title')
    <a href="{{ url('inventory') }}">库存管理</a> >
    <span class="second-level">出库异常</span>
@stop
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 control-search">
            <form action="" method="get" autocomplete="off">
                <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text" value="">至
                <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text" value="">
                <input class="enter control" placeholder="出库单号/售货单号" type="text" value="">
                <button type="button" class=" btn btn-blue-lighter search control search-by-get">搜索</button>
            </form>
        </div>
        <div class="col-sm-12 table-responsive table-wrap">
            <table class="table-bordered table table-center table-title-blue">
                <thead>
                <tr>
                    <th>出库单号</th>
                    <th>售货单号</th>
                    <th>买家名称</th>
                    <th>类型</th>
                    <th>出库人</th>
                    <th>出库时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>20170217111356023</td>
                    <td>5632</td>
                    <td>冬雨批发部</td>
                    <td>系统入库</td>
                    <td>系统</td>
                    <td>2016-10-14   10:08:42</td>
                    <td><a class="color-blue"><i class="iconfont icon-iconmingchengpaixu65"></i>查看</a></td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">

    </script>
@stop
