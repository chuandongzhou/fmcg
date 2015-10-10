@extends('index.menu-master')

@section('subtitle', '我的商品')

@section('right')
    <div class="row controls">
        <div class="col-sm-12">
            <div class="item control-sequence">
                <label>排序:</label>
                <a href="{{ url('my-goods?sort=name'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                   class="btn {{ !isset($get['sort']) || $get['sort'] == 'name' ? 'btn-primary' : 'btn-default' }} control">名称</a>
                <a href="{{ url('my-goods?sort=price'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                   class="btn  control {{ isset($get['sort']) && $get['sort'] == 'price' ? 'btn-primary' : 'btn-default' }}">价格</a>
                {{--<a href="{{ url('my-goods?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"--}}
                   {{--class="btn  control {{ isset($get['sort']) && $get['sort']=='new' ? 'btn-primary' : 'btn-default' }}">新增商品</a>--}}
            </div>
            <form action="{{ url('my-goods') }}"
                  method="get">
                <div class="item control-delivery">
                    <label>配送区域:</label>
                    <select class="address-province" name="province_id"
                            data-id="{{ $get['province_id'] or 0}}">
                        <option>省</option>
                    </select>
                    <select class="address-city" name="city_id"
                            data-id="{{ $get['city_id'] or 0}}">
                        <option>市</option>
                    </select>
                    <select class="address-district" name="district_id"
                            data-id="{{ $get['district_id'] or 0}}">
                        <option>区</option>
                    </select>
                    <select class="address-street" name="street_id"
                            data-id="{{ $get['street_id'] or  0}}">
                        <option>区</option>
                    </select>
                    @foreach(array_except($get , ['province_id' , 'city_id' ,'district_id' , 'name']) as $key=>$val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
                    @endforeach
                </div>
                <div class="item control-name">
                    <label>名称:</label>
                    <input class="control" name="name" value="{{ isset($get['name']) ? $get['name'] : '' }}"
                           type="text">
                    <button class="btn btn-primary control search" type="submit">搜索</button>
                    <a href="{{ url('my-goods/create') }}" class="btn btn-primary control add-goods">新增商品</a>
                </div>
            </form>
        </div>

    </div>
    <div class="row sort">
        @if (!empty(array_except($get , 'name')))
            <div class="col-sm-12 a-menu-panel">
                <div class="sort-item">
                    <a href="{{ url('my-goods') }}" class="pull-left all-results"><span
                                class="fa fa-th-large"></span>
                        全部结果 <span class="fa fa-angle-right"></span> </a>

                    @if (isset($get['category_id']))
                        @foreach($categories as $category)
                            <div class="sort-list">
                                <a class="list-title" href="#">
                                    <span class="title-txt">{{ isset($category['selected']) ? $category['selected']['name'] : '请选择' }}</span><i
                                            class="fa fa-angle-down"></i>
                                </a>

                                <div class="list-wrap">
                                    @foreach($category as $key => $item)
                                        <a href="{{ url('my-goods?category_id=' . $item['level'].$item['id']) }}"
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
                                <a href="{{ url('my-goods?category_id=' . $category['level'].$category['id']) }}"
                                   class="btn  control">
                                    {{ $category['name'] }}
                                </a>
                            @endforeach
                        </p>
                        <a class="more pull-right" href="#"><span>更多</span> <i class="fa fa-angle-down"></i></a>
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
                            <a class="more pull-right" href="#"><span>更多</span> <i class="fa fa-angle-down"></i></a>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(!empty($moreAttr))
                <div class="search-list-item sort-item">
                    <span class="pull-left title-name">更多筛选项 : </span>

                    @foreach($moreAttr as $attr)
                        <div class="sort-list">
                            <a class="list-title">{{ $attr['name'] }} <i class="fa fa-angle-down"></i></a>

                            <div class="list-wrap">
                                @foreach($attr['child'] as $child)
                                    <a href="{{ url('my-goods?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-sm-12">
            <div class="tab-title clearfix">
                <p class="pull-left sequence">
                    <a href="{{ url('my-goods'  . (empty(array_except($get , ['sort'])) ? '' :  '?' . http_build_query(array_except($get , ['sort'])))) }}"
                       class="{{!isset($get['sort']) ? 'active' : ''}} control">全部</a>
                    <a href="{{ url('my-goods?sort=name'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                       class="{{ isset($get['sort']) && $get['sort'] == 'name' ? 'active' : '' }} control">名称</a>
                    <a href="{{ url('my-goods?sort=price'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                       class="control {{ isset($get['sort']) && $get['sort'] == 'price' ? 'active' : '' }}">价格</a>
                    <a href="{{ url('my-goods?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                       class="control {{ isset($get['sort']) && $get['sort']=='new' ? 'active' : '' }}">新增商品</a>
                </p>
            </div>
        </div>
    </div>
    <div class="row list-penal">
        @foreach($goods  as $item)
            <div class="col-sm-3 commodity">
                <a href="{{ url('my-goods/' . $item->id) }}">
                    <img class="commodity-img" src="{{ $item->image_url }}">
                </a>
                <div class="content-panel">
                    <p class="commodity-name">
                        <a href="{{ url('my-goods/' . $item->id) }}">
                            {{ $item->name }}
                        </a>
                    </p>

                    <p class="sell-panel">
                        <span class="money">￥{{ $item->price }}</span>
                        <span class="sales pull-right">销量 : {{ $item->sales_volume }}</span>
                    </p>
                </div>
            </div>
        @endforeach
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
    </script>
@stop
