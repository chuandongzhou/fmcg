@extends('index.index-master')

@section('subtitle')
    {{ $keywords ? : '商品搜索' }}
@stop
@include('includes.jquery-lazeload')

@section('container')
    <div class="container-wrap">
        <div class="container dealer-index index search-page">
            <div class="row sort search-sort">
                @if (!empty(array_except($get , ['name' , 'sort', 'page', 'city_id', 'district_id', 'street_id'])))
                    <div class="col-sm-12 a-menu-panel padding-clear">
                        @if (isset($get['category_id']))
                            <div class="search-list-item sort-item">
                                @if (isset($get['category_id']))
                                    @foreach($categories as $category)
                                        @if(isset($category['selected']))
                                            <div class="sort-list">
                                                <a class="list-title"
                                                   href="{{ url('search?category_id='.$category['selected']['level'].$category['selected']['id']) }}"><span
                                                            class="title-txt">{{  $category['selected']['name']}}</span></a>
                                            </div>
                                            <span class="fa fa-angle-right"></span>
                                        @endif
                                    @endforeach

                                @endif
                                @foreach($searched as $attrId => $name)
                                    <div class="sort-list">
                                        <a class="last-category"
                                           href="{{ url('search?' . http_build_query(array_except($get , ['attr_' . $attrId]))) }}">
                                            {{ $name }}
                                            <i class="fa fa-times"></i></a>
                                    </div>
                                @endforeach
                                <div class="pull-right search-count">
                                    共找到<span class="count">"{{ $goods->total() }}"</span>个相关商品
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="col-sm-12 padding-clear">
                    @if( isset($get['category_id']) && !empty($categories) && !isset($categories[count($categories)-1]['selected']))
                        <div class="search-list-item sort-item sort-item-panel">
                            <span class="pull-left title-name">分类 : </span>
                            <div class="clearfix all-sort-panel">
                                <p class="pull-left all-sort">
                                    @foreach($categories[count($categories)-1] as $cate )
                                        <a href="{{ url('search?category_id='.$cate['level'].$cate['id'] .(isset($get['name']) ? '&name=' . $get['name'] : '')) }}">{{ $cate['name'] }}</a>
                                    @endforeach
                                </p>
                                <a class="more pull-right" href="#"><span>更多</span> <i class="fa fa-angle-down"></i></a>
                            </div>
                        </div>
                    @elseif(!isset($get['category_id']) && !empty($categories))
                        <div class="search-list-item sort-item sort-item-panel">
                            <span class="pull-left title-name">分类 : </span>

                            <div class="clearfix all-sort-panel">
                                <p class="pull-left all-sort">
                                    @foreach($categories as $key => $category)
                                        <a href="{{ url('search?category_id=' . $category['level'].$category['id'] . (isset($get['name']) ? '&name=' . $get['name'] : '' )) }}"
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
                        @foreach($moreAttr as $attr)
                            @if(isset($attr['child']))
                                <div class="search-list-item sort-item sort-item-panel">
                                    <span class="pull-left title-name">{{ $attr['name'] }} : </span>

                                    <div class="clearfix all-sort-panel">
                                        <p class="pull-left all-sort">
                                            @foreach($attr['child'] as $child)
                                                <a href="{{ url('search?attr_' . $attr['attr_id'] . '=' . $child['attr_id']  . '&' . http_build_query($get)) }}">{{ $child['name'] }}</a>
                                            @endforeach
                                        </p>
                                        <a class="more pull-right" href="#"><span>更多</span> <i
                                                    class="fa fa-angle-down"></i></a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
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
                               class="control {{ isset($get['sort']) && $get['sort']=='new' ? 'active' : '' }}">最新</a>
                        </p>

                        <p class="pull-left area">
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
                    {!! $goods->appends(array_filter(array_except($get , ['province_id' ,'city_id'])))->render() !!}
                </div>
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
            var url = '{!! url('search'  . (empty(array_except($get , ['province_id' ,'city_id' ,'district_id' ])) ? '' :  '?' . http_build_query(array_except($get ,  ['province_id' ,'city_id' ,'district_id' ])))) !!}' + address;
            location.href = url;
        })
    </script>
@stop