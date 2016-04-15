@extends('index.master')

@section('subtitle' , '店铺商品搜索')

@section('container')
    @include('index.shop-search')
    <div class="container dealer-index index search-page">
        <div class="row sort search-sort">
            @if (!empty(array_except($get , ['name', 'sort', 'page', 'city_id', 'district_id', 'street_id'])))
                <div class="col-sm-12 a-menu-panel">
                    <div class="sort-item">
                        <a href="{{ url('shop/' . $shop->id . '/search') }}" class="pull-left all-results"><span
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
                                            <a href="{{ url('shop/' . $shop->id . '/search?category_id=' . $item['level'].$item['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}"
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
                                   href="{{ url('shop/' . $shop->id . '/search?' . http_build_query(array_except($get , ['attr_' . $attrId]))) }}"> {{ $name }}
                                    <i class="fa fa-times"></i></a>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif
            <div class="col-sm-12">
                @if( !isset($get['category_id']) && !empty($categories))
                    <div class="search-list-item sort-item sort-item-panel">
                        <span class="pull-left title-name">分类 : </span>

                        <div class="clearfix all-sort-panel">
                            <p class="pull-left all-sort">
                                @foreach($categories as $key => $category)
                                    <a href="{{ url('shop/' . $shop->id . '/search?category_id=' . $category['level'].$category['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}"
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
                                        <a href="{{ url('shop/' . $shop->id . '/search?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}"
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
                    <div class="search-list-item sort-item">
                        <span class="pull-left title-name">更多筛选项 : </span>

                        @foreach($moreAttr as $attr)
                            <div class="sort-list">
                                <a class="list-title">{{ $attr['name'] }} <i class="fa fa-angle-down"></i></a>

                                <div class="list-wrap">
                                    @if(isset($attr['child']))
                                        @foreach($attr['child'] as $child)
                                            <a href="{{ url('shop/' . $shop->id . '/search?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="tab-title clearfix">
                    <p class="pull-left sequence">
                        <a href="{{ url('shop/' . $shop->id . '/search'  . (empty(array_except($get , ['sort'])) ? '' :  '?' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{!isset($get['sort']) ? 'active' : ''}} control">全部</a>
                        <a href="{{ url('shop/' . $shop->id . '/search?sort=name'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="{{ isset($get['sort']) && $get['sort'] == 'name' ? 'active' : '' }} control">名称</a>
                        <a href="{{ url('shop/' . $shop->id . '/search?sort=price'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort'] == 'price' ? 'active' : '' }}">价格</a>
                        <a href="{{ url('shop/' . $shop->id . '/search?sort=new'  . (empty(array_except($get , ['sort'])) ? '' :  '&' . http_build_query(array_except($get , ['sort'])))) }}"
                           class="control {{ isset($get['sort']) && $get['sort']=='new' ? 'active' : '' }}">最新</a>
                    </p>

                    <p class="pull-right">
                        <span>配送区域</span>
                        <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                                class="address-province address hide"></select>
                        <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                                class="address-city address hide"></select>
                        <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                                class="address-district address"> </select>
                        <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                                class="address-street address useless-control hide"> </select>
                    </p>
                </div>
            </div>
        </div>
        <div class="row list-penal">
            @include('includes.goods-list' , ['goods'=> $goods])
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                {!! $goods->appends(array_filter(array_except($get ,['province_id' ,'city_id'])))->render() !!}
            </div>
        </div>
    </div>
    @include('includes.cart')
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        displayList();
        joinCart();
        numChange();
        $('select[name="district_id"]').change(function () {
            var districtControl = $(this),
                    address = districtControl.val() ? '{!! empty(array_except($get ,  ['province_id' ,'city_id' ,'district_id' ])) ? '?' : '&' !!}district_id=' + districtControl.val() : '';
            var url = '{!! url('shop/'. $shop->id . '/search'  . (empty(array_except($get ,['province_id' ,'city_id' ,'district_id' ])) ? '' :  '?' . http_build_query(array_except($get ,  ['province_id' ,'city_id' ,'district_id' ])))) !!}' + address;
            location.href = url;
        })
    </script>
@stop
