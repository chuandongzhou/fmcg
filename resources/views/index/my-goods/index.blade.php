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
                <a href="{{ url('my-goods?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                   class="btn  control {{ isset($get['sort']) && $get['sort']=='new' ? 'btn-primary' : 'btn-default' }}">新增商品</a>
            </div>
            <form action="{{ url('my-goods') }}"
                  method="get">
                <div class="item control-delivery">
                    <label>配送区域:</label>
                    <select class="address-province" name="province_id"
                            data-id="{{ isset($get['province_id']) ? $get['province_id'] : 0}}">
                        <option>省</option>
                    </select>
                    <select class="address-city" name="city_id"
                            data-id="{{ isset($get['city_id']) ? $get['city_id'] : 0}}">
                        <option>市</option>
                    </select>
                    <select class="address-district" name="district_id"
                            data-id="{{ isset($get['district_id']) ? $get['district_id'] : 0}}">
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
                </div>
            </form>
        </div>

    </div>
    <div class="row sort">
        <div class="col-sm-12 sort-panel">

            <div class="sort-item clearfix">
                <label class="pull-left  title-name">分类 : </label>

                <div class="clearfix all-sort-panel">
                    <p class="pull-left all-sort">
                        <a href="{{ url('my-goods' . (empty(array_except($data , ['category_id' , 'attr'])) ? '' :  '?' . http_build_query(array_except($data , ['category_id' , 'attr']))) )}}"
                           class="btn  control {{ !isset($get['category_id']) ? 'btn-primary' : '' }}">
                            全部
                        </a>
                        @foreach($categories as $key => $category)
                            <a href="{{ url('my-goods?category_id=' . $category['level'].$category['id'] . (empty(array_except($data , ['category_id' , 'attr'])) ? '' :  '&' . http_build_query(array_except($data , ['category_id' , 'attr'])))) }}"
                               class="btn  control {{ isset($get['category_id']) &&  $category['level'].$category['id'] == $get['category_id'] ? 'btn-primary' : '' }}">
                                {{ $category['name'] }}
                            </a>  &nbsp; &nbsp;
                        @endforeach
                    </p>
                    <a class="more pull-right" href="javascript:void(0)"><span>更多</span> <i
                                class="fa fa-angle-down"></i></a>
                </div>
            </div>
            @foreach($attrs as $attr)

                <div class="sort-item clearfix">
                    <label class="pull-left title-name">{{ $attr['name'] }} : </label>

                    <div class="clearfix all-sort-panel">
                        <p class="pull-left all-sort">
                            <a href="{{ url('my-goods'. (empty(array_except($get , ['attr_' . $attr['id']])) ? '' : '?'. http_build_query(array_except($get , ['attr_' . $attr['id']])))) }}"
                               class="btn  control {{ !isset($get['attr_' . $attr['id']]) ? 'btn-primary' : '' }}">
                                全部
                            </a>

                            @foreach($attr['child'] as $child)
                                <a href="{{ url('my-goods?attr_' . $attr['id'] . '=' . $child['id']  . '&' . http_build_query(array_except($get , ['attr_' . $attr['id']]))) }}"
                                   class="btn  control {{ isset($data['attr']) && in_array($child['id'] , $data['attr']) ? 'btn-primary' : '' }}">
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
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"/>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        displayList();
    </script>
@stop
