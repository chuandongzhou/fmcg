@extends('index.menu-master')
@section('subtitle', '库存管理')
@section('top-title')
    <a href="{{ url('inventory') }}">库存管理</a> >
    <span class="second-level">库存管理</span>
@stop

@section('right')
    <div class="row goods-tables margin-clear  search-page">
        <div class="col-sm-12 commodity-class search-sort sort">
            @if (!empty(array_except($data , ['name' , 'sort', 'page'])))
                <div class="col-sm-12 a-menu-panel padding-clear">
                    @if (isset($data['category_id']))
                        <div class="search-list-item sort-item">
                            @if (isset($data['category_id']))
                                @foreach($categories as $key => $category)
                                    @if(isset($category['selected']))
                                        @if(array_keys($categories)[0]==$key)
                                            <select class="control select-category">
                                                <option value="{{ url('inventory') }}">全部分类</option>
                                                @foreach($category as $key => $item)
                                                    <option value="{{ url('inventory?category_id=' . $item['level'].$item['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : '' )) }}" {{ $category['selected']['id']==$item['id']?'selected':'' }}>
                                                        {{ $item['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <div class="sort-list">
                                                <a class="list-title"
                                                   href="{{ url('inventory?category_id='.$category['selected']['level'].$category['selected']['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : '') )}}"><span
                                                            class="title-txt">{{  $category['selected']['name']}}</span></a>
                                            </div>
                                        @endif
                                        <span class="fa fa-angle-right"></span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            @endif
            <div class="col-sm-12 padding-clear">
                @if( isset($data['category_id']) && !empty($categories) && !isset($categories[count($categories)-1]['selected']))
                    <div class="search-list-item sort-item sort-item-panel">
                        <span class="pull-left title-name">分类 : </span>
                        <div class="clearfix all-sort-panel">
                            <div class="pull-left all-sort">
                                @foreach($categories[count($categories)-1] as $cate )
                                    <a href="{{ url('inventory?category_id='.$cate['level'].$cate['id']. (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : ''))  }}">{{ $cate['name'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif(!isset($data['category_id']) && !empty($categories))
                    商品分类 :
                    <select class="control select-category">
                        <option value="{{ url('inventory') }}">全部分类</option>
                        @foreach($categories as $key => $category)
                            <option value="{{ url('inventory?category_id=' . $category['level'].$category['id'] . (isset($data['nameOrCode']) ? '&nameOrCode=' . $data['nameOrCode'] : '' )) }}">
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <form action="{{url('inventory')}}" method="get" autocomplete="off">
            <div class="col-sm-12 controls">
                <div class="item-panel">
                    <div class="item inventory-name-input">
                        <input class="control" name="nameOrCode" value="{{$data['nameOrCode']}}"
                               placeholder="商品名称/商品条形码" type="text">
                        @foreach(array_only($data , ['category_id']) as $key=>$val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
                        @endforeach
                    </div>
                    <div class="item ">
                        <button class="btn btn-blue-lighter control search search-by-get" type="submit">搜索</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-sm-12  goods-table-panel">
            <table class="table table-bordered table-title table-width">
                <thead>
                <tr>
                    <th>商品名</th>
                    <th>商品条形码</th>
                    <th>价格</th>
                    <th>库存</th>
                    <th>分类</th>
                    <th>操作</th>
                </tr>
                </thead>
            </table>
            <div class="goods-table">
                <table class="table table-width">
                    <tbody>
                    @if(isset($goods))
                        @foreach($goods as $item)
                            <tr>
                                <td>
                                    <img class="store-img lazy" data-original="{{ $item->image_url }}"
                                         src="{{ $item->image_url }}">
                                    <a class="product-name ellipsis"
                                       href="{{ url('goods/' . $item->id) }}"
                                       title="{{ $item->name }}"> {{$item->name}}</a>
                                </td>
                                <td>
                                    {{$item->bar_code}}
                                </td>
                                <td>
                                    <p>{{ $item->price_retailer}}元</p>

                                    <p>{{ $item->price_wholesaler }}元 (批)</p>
                                </td>
                                <td>
                                    {{$item->surplus_inventory}}
                                </td>
                                <td>{{ isset($cate[$item->id]) ? implode('/',$cate[$item->id]) : '' }} {{--食品生鲜/休闲食品/膨化食品--}}</td>
                                <td class="operating text-center">
                                    <a href="{{url('inventory/out-create'). '/' .$item->id}}" class="edit">出库</a>
                                    <a href="{{url('inventory/in-create'). '/' .$item->id}}" class="edit">入库</a>
                                    <a href="{{url('inventory/goods-inventory-detail'). '/' .$item->id}}" class="edit">明细</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-12 text-right">
            {!! $goods->appends($data)->render() !!}
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        $('.select-category').change(function () {
            window.location.href = $(this).val();
        });
    </script>
@stop
