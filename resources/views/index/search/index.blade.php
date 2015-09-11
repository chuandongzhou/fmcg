@extends('index.index-master')

@section('container')
    <div class="container dealer-index index search-page">
        <div class="row sort">
            <div class="col-sm-12 sort-panel">
                <div class="sort-item search-list-item clearfix">
                    <label class="pull-left  title-name">分类 : </label>

                    <div class="clearfix all-sort-panel">
                        <p class="pull-left all-sort">
                            <a href="{{ url('search' . (empty(array_except($data , ['category_id' , 'attr'])) ? '' :  '?' . http_build_query(array_except($data , ['category_id' , 'attr']))) )}}"
                               class="btn  control {{ !isset($get['category_id']) ? 'active' : '' }}">
                                全部
                            </a>
                            @foreach($categories as $key => $category)
                                <a href="{{ url('search?category_id=' . $category['level'].$category['id'] . (empty(array_except($data , ['category_id' , 'attr'])) ? '' :  '&' . http_build_query(array_except($data , ['category_id' , 'attr'])))) }}"
                                   class="btn  control {{ isset($get['category_id']) &&  $category['level'].$category['id'] == $get['category_id'] ? 'active' : '' }}">
                                    {{ $category['name'] }}
                                </a>  &nbsp; &nbsp;
                            @endforeach
                        </p>
                        <a class="more pull-right" href="javascript:void(0)"><span>更多</span> <i
                                    class="fa fa-angle-down"></i></a>
                    </div>
                </div>
                @foreach($attrs as $attr)
                    <div class="sort-item  search-list-item clearfix">
                        <label class="pull-left title-name">{{ $attr['name'] }} : </label>

                        <div class="clearfix all-sort-panel">
                            <p class="pull-left all-sort">
                                <a href="{{ url('search'. (empty(array_except($get , ['attr_' . $attr['id']])) ? '' : '?'. http_build_query(array_except($get , ['attr_' . $attr['id']])))) }}"
                                   class="btn  control {{ !isset($get['attr_' . $attr['id']]) ? 'active' : '' }}">
                                    全部
                                </a>

                                @foreach($attr['child'] as $child)
                                    <a href="{{ url('search?attr_' . $attr['id'] . '=' . $child['id']  . '&' . http_build_query(array_except($get , ['attr_' . $attr['id']]))) }}"
                                       class="btn  control {{ isset($data['attr']) && in_array($child['id'] , $data['attr']) ? 'active' : '' }}">
                                        {{ $child['name'] }}
                                    </a>
                                @endforeach
                            </p>
                            <a class="more pull-right" href="javascript:void(0)"><span>更多</span> <i
                                        class="fa fa-angle-down"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-sm-12">
                <div class="title clearfix">
                    <p class="pull-left">
                        <a href="{{ url('search'  . (empty(array_except($get , ['sort'])) ? '' :  '?' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{!isset($get['sort']) ? 'active' : 'btn-default'}} control">全部</a>
                        <a href="{{ url('search?sort=name'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{ isset($get['sort']) && $get['sort'] == 'name' ? 'active' : 'btn-default' }} control">名称</a>
                        <a href="{{ url('search?sort=price'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort'] == 'price' ? 'active' : 'btn-default' }}">价格</a>
                        <a href="{{ url('search?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort']=='new' ? 'active' : 'btn-default' }}">新增商品</a>
                    </p>

                    <p class="pull-right">
                        <span>配送区域</span>
                        <select name="province_id" class="address-province"></select>
                        <select name="city_id" class="address-city"></select>
                        <select name="district_id" class="address-district"> </select>
                        <select name="street_id" class="address-street"> </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            @foreach($goods  as $item)
                <div class="col-sm-3 commodity">
                    <img class="commodity-img" src="{{ $item->image_url }}">

                    <p class="commodity-name">{{ $item->name }}</p>

                    <p class="sell-panel">
                        <span class="money">￥{{ $item->price }}</span>
                        <span class="sales pull-right">销量 : {{ $item->sales_volume }}</span>
                    </p>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
               {{ $goods->render() }}
            </div>
        </div>
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
