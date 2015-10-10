@extends('index.index-master')

@section('container')
    <div class="container dealer-index index search-page">
        <div class="row sort search-sort">
            @if (!empty(array_except($get , 'name')))
                <div class="col-sm-12 a-menu-panel">
                    <div class="search-list-item sort-item">
                        <a href="{{ url('search') }}" class="pull-left all-results"><span class="fa fa-th-large"></span></a>
                        @if (isset($get['category_id']))
                            @foreach($categories as $category)
                                <div class="sort-list">
                                    <a class="list-title" href="#">
                                        <span class="title-txt">{{ isset($category['selected']) ? $category['selected']['name'] : '请选择' }}</span><i
                                                class="fa fa-angle-down"></i>
                                    </a>

                                    <div class="list-wrap">
                                        @foreach($category as $key => $item)
                                            <a href="{{ url('search?category_id=' . $item['level'].$item['id']) }}"
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
                                   href="{{ url('search?' . http_build_query(array_except($get , ['attr_' . $attrId]))) }}"> {{ $name }}
                                    <i class="fa fa-times"></i></a>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif
            <div class="col-sm-12 padding-clear">
                @if( !isset($get['category_id']))
                    <div class="search-list-item sort-item sort-item-panel">
                        <span class="pull-left title-name">分类 : </span>

                        <div class="clearfix all-sort-panel">
                            <p class="pull-left all-sort">
                                @foreach($categories as $key => $category)
                                    <a href="{{ url('search?category_id=' . $category['level'].$category['id']) }}"
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
                                        <a href="{{ url('search?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}"
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
                                        <a href="{{ url('search?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-sm-12 padding-clear">
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
                    <a href="{{ url('goods/' . $item->id) }}">
                        <img class="commodity-img" src="{{ $item->image_url }}">
                    </a>

                    <div class="content-panel">
                        <p class="commodity-name"><a href="{{ url('goods/' . $item->id) }}">{{ $item->name }}</a></p>

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