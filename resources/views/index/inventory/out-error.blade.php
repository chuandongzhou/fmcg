@extends('index.manage-master')
@section('subtitle', '出库异常')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">出库异常</span>
                </div>
            </div>
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
                                   data-content="系统自动出库出现异常,请手动出库">
                                </a>
                                操作
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($errorLists))
                            @foreach($errorLists as $list)
                                <tr>
                                    <td>
                                        <div class="product-name" title="{{$list->goods->name ?? ''}}">
                                            {{$list->goods->name ?? ''}}
                                        </div>
                                    </td>
                                    <td>{{$list->goods->bar_code ?? ''}}</td>
                                    <td>{{$list->order_id ?? ''}}</td>
                                    <td>{{$list->updated_at}}</td>
                                    <td>
                                        <a class="color-blue viewDetail"
                                           href="{{url('inventory/out-create/'.$list->goods->id)}}">我要出库</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $errorLists->render() !!}
                </div>
            </div>
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
