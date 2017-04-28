@extends('index.menu-master')
@section('subtitle', '入库异常')
@section('top-title')
    <a href="{{ url('inventory') }}">库存管理</a> >
    <span class="second-level">入库异常</span>
@stop
@section('right')
    <div class="row delivery">
        <div class="col-sm-12 control-search">
            <a type="button" class="btn btn-default control" href="javascript:history.back()">返回</a>
        </div>
        <div class="col-sm-12 table-responsive wareh-details-table">
            <table class="table-bordered table table-center public-table">
                <thead>
                <tr>
                    <th>商品名称</th>
                    <th>商品条形码</th>
                    <th>进货单号</th>
                    <th>日期</th>
                    <th>
                        <a class="iconfont icon-tixing" title=""
                           data-container="body" data-toggle="popover" data-placement="bottom"
                           data-content="我的商品库里没有与此匹配的商品请新先新增商品后再手动入库">
                        </a>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="product-name" title="测试商品000002测试商品000002测试商品000002测试商品000002">测试商品000002测试商品000002测试商品000002测试商品000002</div>
                    </td>
                    <td>20170217111356023</td>
                    <td>5632</td>
                    <td>2015-11-24  19:56:42</td>
                    <td>
                        <a class="edit">新增商品</a>
                        <a class="color-blue"  data-target="#myModal" data-toggle="modal">查看</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="product-name" title="测试商品000002测试商品000002测试商品000002测试商品000002">测试商品000002测试商品000002测试商品000002测试商品000002</div>
                    </td>
                    <td>20170217111356023</td>
                    <td>5632</td>
                    <td>2015-11-24  19:56:42</td>
                    <td>
                        <a class="gray">已增商品</a>
                        <a class="edit">新增商品</a>
                        <a class="color-blue" data-target="#myModal" data-toggle="modal">查看</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="product-name" title="测试商品000002测试商品000002测试商品000002测试商品000002">测试商品000002测试商品000002测试商品000002测试商品000002</div>
                    </td>
                    <td>20170217111356023</td>
                    <td>5632</td>
                    <td>2015-11-24  19:56:42</td>
                    <td>
                        <a class="gray">已增商品</a>
                        <a class="green">我要入库</a>
                        <a class="color-blue" data-target="#myModal" data-toggle="modal">查看</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-12 text-right">
            <ul class="pagination management-pagination"><li class="disabled"><span>«</span></li> <li class="active"><span>1</span></li><li><a href="http://192.168.2.66/order-sell?page=2">2</a></li><li><a href="http://192.168.2.66/order-sell?page=3">3</a></li> <li><a href="http://192.168.2.66/order-sell?page=2" rel="next">»</a></li></ul>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $("[data-toggle='popover']").popover();
        })
    </script>
@stop
