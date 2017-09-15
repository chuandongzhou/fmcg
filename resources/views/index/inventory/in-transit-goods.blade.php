@extends('index.manage-master')
@section('subtitle', '在途商品')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">在途商品</span>
                </div>
            </div>
            <div class="row goods-tables margin-clear">
                <div class="col-sm-12  goods-table-panel">
                    <div class="goods-table">
                        <table class="table table-width table-title table-center" id="good-ontheway">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>商品条形码</th>
                                <th>在途总数</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($inTransitGoods))
                                @foreach($inTransitGoods as $item)
                                    <tr>
                                        <td>
                                            <img class="store-img lazy"
                                                 data-original="{{$item->image_url}}"
                                                 src="{{$item->image_url}}">
                                            <a class="product-name" href="{{url('my-goods/'.$item->id)}}">
                                                {{$item->name}}</a>
                                        </td>
                                        <td>
                                            {{$item->bar_code}}
                                        </td>
                                        <td>{{$item->count}}</td>
                                        <td class="operating text-center">
                                            <a href="{{url('inventory/in-transit-goods-detail/'.$item->id)}}"
                                               class="color-blue"><i class="iconfont icon-chakan"></i>
                                                查看</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-12 text-right">
                    @if(count($inTransitGoods)){!! $inTransitGoods->render() !!}@endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            formSubmitByGet();
        })
    </script>
@stop
