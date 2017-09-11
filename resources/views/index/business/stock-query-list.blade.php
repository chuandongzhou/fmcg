@extends('index.manage-master')
@section('subtitle', '业务管理-库存查询')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <!--页面中间内容开始-->
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <a href="{{ url('business/salesman-customer?type=supplier') }}">供应商管理</a> >
                    <span class="second-level">库存查询</span>
                </div>
            </div>
            <div class="row goods-tables margin-clear">
                <div class="col-sm-4 query-title">
                    商户名称：{{$customer->name}}
                </div>
                <div class="col-sm-8 query-title">
                    商户地址：{{$customer->business_address_name}}
                </div>
                <div class="col-sm-12 controls">
                    <form action="{{url('business/salesman-customer/'.$customer->id.'/stock')}}" method="get"
                          autocomplete="off">
                        <div class="item-panel query-controls">
                            <div class="item control-name">
                                <input class="control" name="name_code" placeholder="商品名称/商品条形码" type="text"
                                       value="{{$data['name_code']}}">
                            </div>
                            <div class="item btn-item">
                                <button class="btn btn-blue-lighter search search-by-get" type="submit">搜索</button>
                            </div>
                            <div class="item btn-item">
                                <a href="{{url('business/salesman-customer/'.$customer->id.'/stock?action=exp'.(isset($data['name_code']) ? '&name_code='.$data['name_code'] : '' ))}}"
                                   class="btn btn-border-blue">导出</a>
                            </div>
                            <div class="item btn-item">
                                <a href="javascript:history.back()" class="btn btn-border-blue ">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-12  goods-table-panel">
                    <table class="table table-bordered table-title table-width">
                        <thead>
                        <tr>
                            <th>商品名</th>
                            <th>商品条形码</th>
                            <th>当前实时库存</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="goods-table query-table">
                        <table class="table table-width">
                            <tbody>
                            @foreach($cgl as $goods)
                                <tr>
                                    <td>
                                        <img class="store-img lazy"
                                             data-original="http://192.168.2.65//upload/file/2016/06/02/574faa6603e88.png"
                                             src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC">
                                        <a class="product-name ellipsis" href="javascript:;">
                                            {{$goods->name}}</a>
                                    </td>
                                    <td>{{$goods->bar_code}}</td>
                                    <td>{{$goods->surplus_inventory}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-right">
                    {!! $cgl->appends($data)->render() !!}
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
