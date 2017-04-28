@extends('index.menu-master')
@section('subtitle', '在途商品')
@section('top-title')
    <a href="{{ url('inventory') }}">库存管理</a> >
    <span class="second-level">在途商品</span>
@stop
@section('right')
    <div class="row goods-tables margin-clear">
        <div class="col-sm-12  goods-table-panel">
            <div class="goods-table">
                <table class="table table-width table-title table-center good-ontheway">
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
                                         data-original="{{$item->goods->image_url}}"
                                         src="{{$item->goods->image_url}}">
                                    <a class="product-name" href="http://192.168.2.65/my-goods/324">
                                        {{$item->goods->name}}</a>
                                </td>
                                <td>
                                    {{$item->goods->bar_code}}
                                </td>
                                <td>{{$item->count}}</td>
                                <td class="operating text-center">
                                    <a href="{{url('inventory/in-transit-goods-detail/'.$item->goods->id)}}" class="color-blue"><i class="iconfont icon-iconmingchengpaixu65"></i>
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
            {!! $inTransitGoods->render() !!}
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
