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
                <div class="tab-title clearfix">
                    <p class="pull-left sequence">
                        <a href="{{ url('search'  . (empty(array_except($get , ['sort'])) ? '' :  '?' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{!isset($get['sort']) ? 'active' : ''}} control">全部</a>
                        <a href="{{ url('search?sort=name'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{ isset($get['sort']) && $get['sort'] == 'name' ? 'active' : '' }} control">名称</a>
                        <a href="{{ url('search?sort=price'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort'] == 'price' ? 'active' : '' }}">价格</a>
                        <a href="{{ url('search?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort']=='new' ? 'active' : '' }}">新增商品</a>
                    </p>

                    <p class="pull-right">
                        <span>配送区域</span>
                        <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                                class="address-province address"></select>
                        <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                                class="address-city address"></select>
                        <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                                class="address-district address"> </select>
                        <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                                class="address-street address"> </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            @foreach($goods  as $item)
                <div class="col-sm-3 commodity">
                    <img class="commodity-img" src="{{ $item->image_url }}">

                    <div class="content-panel">
                        <p class="commodity-name">{{ $item->name }}</p>

                        <p class="sell-panel">
                            <span class="money">￥{{ $item->price }}</span>
                            <span class="sales pull-right">销量 : {{ $item->sales_volume }}</span>
                        </p>
                    </div>
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
        $('select.address').change(function () {
            var provinceControl = $('select[name="province_id"]'),
                    cityControl = $('select[name="city_id"]'),
                    districtControl = $('select[name="district_id"]'),
                    streetControl = $('select[name="street_id"]'),
                    address = provinceControl.val() ? '{!! empty(array_except($get , ['province_id', 'city_id', 'district_id', 'street_id'])) ? '?' : '&' !!}province_id=' + provinceControl.val() : '';
            address += cityControl.val() ? '&city_id=' + cityControl.val() : '';
            address += districtControl.val() ? '&district_id=' + districtControl.val() : '';
            address += streetControl.val() && address ? '&street_id=' + streetControl.val() : '';
            var url = '{{ url('search'  . (empty(array_except($get , ['province_id', 'city_id', 'district_id', 'street_id'])) ? '' :  '?' . http_build_query(array_except($get , ['province_id', 'city_id', 'district_id', 'street_id'])))) }}' + address;

            location.href = url;
        })
    </script>
@stop
