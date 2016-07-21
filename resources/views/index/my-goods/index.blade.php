@extends('index.menu-master')

@section('subtitle', '我的商品')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> &rarr;
    我的商品
@stop
@include('includes.jquery-lazeload')

@section('right')
    <div class="row controls">
        <div class="col-sm-12 sort">
            <div class="row">
                @if (!empty(array_except($get , ['name' , 'sort', 'page'])))
                    <div class="col-sm-12 a-menu-panel">
                        <div class="sort-item">
                            <a href="{{ url('my-goods') }}" class="pull-left all-results"><span
                                        class="fa fa-th-large"></span>
                                全部结果 <span class="fa fa-angle-right"></span> </a>

                            @if (isset($get['category_id']))
                                @foreach($categories as $category)
                                    <div class="sort-list">
                                        <a class="list-title" href="javascript:">
                                            <span class="title-txt">{{ isset($category['selected']) ? $category['selected']['name'] : '请选择' }}</span><i
                                                    class="fa fa-angle-down"></i>
                                        </a>

                                        <div class="list-wrap">
                                            @foreach($category as $key => $item)
                                                <a href="{{ url('my-goods?category_id=' . $item['level'].$item['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}"
                                                   class="btn  control">
                                                    {{ $item['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="fa fa-angle-right"></span>
                                @endforeach
                            @endif

                            @foreach($searched as $attrId => $name)
                                <div class="sort-list">
                                    <a class="last-category"
                                       href="{{ url('my-goods?' . http_build_query(array_except($get , ['attr_' . $attrId]))) }}"> {{ $name }}
                                        <i class="fa fa-times"></i></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="col-sm-12">
                    @if( !isset($get['category_id']))
                        <div class="search-list-item sort-item sort-item-panel">
                            <span class="pull-left title-name">分类 : </span>

                            <div class="clearfix all-sort-panel">
                                <p class="pull-left all-sort">
                                    @foreach($categories as $key => $category)
                                        <a href="{{ url('my-goods?category_id=' . $category['level'].$category['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}"
                                           class="btn  control">
                                            {{ $category['name'] }}
                                        </a>
                                    @endforeach
                                </p>
                                <a class="more pull-right" href="javascript:"><span>更多</span> <i
                                            class="fa fa-angle-down"></i></a>
                            </div>
                        </div>
                    @endif

                    @foreach($attrs as $attr)
                        @if(isset($attr['child']))
                            <div class="search-list-item sort-item sort-item-panel">
                                <span class="pull-left title-name">{{ $attr['name'] }} : </span>

                                <div class="clearfix all-sort-panel">
                                    <p class="pull-left all-sort">
                                        @foreach($attr['child'] as $child)
                                            <a href="{{ url('my-goods?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}"
                                               class="btn  control">
                                                {{ $child['name'] }}
                                            </a>
                                        @endforeach
                                    </p>
                                    <a class="more pull-right" href="javascript:"><span>更多</span> <i
                                                class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!empty($moreAttr))
                        <div class="sort-item">
                            <span class="pull-left title-name">更多筛选项 : </span>

                            @foreach($moreAttr as $attr)
                                <div class="sort-list">
                                    <a class="list-title">{{ $attr['name'] }} <i class="fa fa-angle-down"></i></a>

                                    <div class="list-wrap">
                                        @if(isset($attr['child']))
                                            @foreach($attr['child'] as $child)
                                                <a href="{{ url('my-goods?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-12 ">
            <div class="item-panel">
                <form action="{{ url('my-goods') }}" method="get" autocomplete="off">
                    <div class="item control-name">
                        <label>名称:</label>
                        <input class="control" name="name" value="{{ isset($get['name']) ? $get['name'] : '' }}"
                               type="text">
                    </div>
                    <div class="item control-status">
                        <label>状态:</label>
                        <select class="status" name="status">
                            <option value="">请选择</option>
                            @foreach(cons()->valueLang('goods.status') as $status => $statusName)
                                <option value="{{ $status }}" {{ isset($get['status']) && $get['status'] == $status ? 'selected' : '' }}>
                                    已{{ $statusName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="item control-delivery">
                        <label>配送区域:</label>
                        <select class="address-province" name="province_id"
                                data-id="{{ $get['province_id'] or 0}}">
                        </select>
                        <select class="address-city" name="city_id"
                                data-id="{{ $get['city_id'] or 0}}">
                        </select>
                        <select class="address-district" name="district_id"
                                data-id="{{ $get['district_id'] or 0}}">
                        </select>
                        <select class="address-street hidden useless-control" name="street_id"
                                data-id="{{ $get['street_id'] or  0}}">
                        </select>
                        @foreach(array_except($get , ['province_id' , 'city_id' ,'district_id', 'street_id' , 'name', 'page', 'status']) as $key=>$val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
                        @endforeach
                    </div>
                    <div class="item pull-right">
                        <button class="btn btn-primary control search search-by-get" type="submit">搜索</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row goods-tables">
        <form class="form-horizontal ajax-form" method="put"
              action="{{ url('api/v1/my-goods/batch-shelve') }}"
              data-help-class="col-sm-push-1 col-sm-10" data-done-url="{{ url('my-goods') }}"
              autocomplete="off">


            <div class="col-sm-12 operating">
                <label class="all-check"><input type="checkbox" id="parent"> 全选</label>

                <button class="btn batch" type="submit" data-data='{"status":"1"}'>批量上架</button>
                <button class="btn batch" type="submit" data-data='{"status":"0"}'>批量下架</button>

                <div class="pull-right text-right">
                    {!! $goods->appends($data)->render() !!}
                </div>
            </div>
            <div class="col-sm-12  goods-table-panel">
                <table class="table table-bordered table-title table-width">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>价格</th>
                        <th>最低购买数</th>
                        <th>分类</th>
                        <th>更新时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                </table>
                <div class="goods-table">
                    <table class="table table-bordered   table-width">
                        @foreach($goods  as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="child" name="ids[]" value="{{ $item->id }}">
                                    <img class="store-img lazy" data-original="{{ $item->image_url }}">
                                    <a class="product-name ellipsis"
                                       href="{{ url('my-goods/' . $item->id) }}"> {{ $item->name }}</a>
                                </td>
                                <td>
                                    <p>{{ $item->price }}元</p>
                                    @if(auth()->user()->type == cons('user.type.supplier'))
                                        <p>{{ $item->price_wholesaler }}元 (批)</p>
                                    @endif
                                </td>
                                <td>
                                    <p> {{ $item->min_num }}</p>
                                    @if(auth()->user()->type == cons('user.type.supplier'))
                                        <p>{{ $item->min_num_wholesaler }} (批)</p>
                                    @endif
                                </td>
                                <td>
                                    {{ isset($goodsCateName[$item->category_id]) ? $goodsCateName[$item->category_id] : '' }}
                                </td>
                                <td>{{ $item->updated_at }}</td>
                                <td>已{{ cons()->valueLang('goods.status' ,$item->status) }}</td>
                                <td class="operating text-center">
                                    @if(!$item->is_mortgage_goods)
                                        <a href="javascript:" data-id="{{ $item->id }}" data-method="post"
                                           data-url="{{ url('api/v1/my-goods/' . $item->id . '/mortgage') }}"
                                           class="no-form mortgage" title="设为抵费商品">抵费</a>
                                    @endif

                                    <a href="{{ url('my-goods/' . $item->id . '/edit') }}" class="editor">编辑</a>
                                    {{--<a href="javascript:" data-id="{{ $item->id }}" data-method="put"--}}
                                    {{--data-data='{ "status": "{{ !$item->status }}" }'--}}
                                    {{--data-done-then="refresh"--}}
                                    {{--class="no-form shelve">--}}

                                    <a href="javascript:" data-method="put"
                                       data-url="{{ url('api/v1/my-goods/shelve')}}"
                                       data-status="{{ $item->status }}"
                                       data-data='{ "id": "{{ $item->id }}" }'
                                       data-on='上架'
                                       data-off='下架'
                                       class="ajax-no-form">
                                        {{ cons()->valueLang('goods.status' , !$item->status) }}
                                    </a>
                                    <a class="delete-no-form" data-method="delete"
                                       data-url="{{ url('api/v1/my-goods/' . $item->id) }}" href="javascript:">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </form>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        displayList();
        formSubmitByGet();
        myGoodsFunc();
        onCheckChange('#parent', '.child');
    </script>
@stop
