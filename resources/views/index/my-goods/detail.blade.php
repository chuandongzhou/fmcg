@extends('index.menu-master')

@section('subtitle', '商品详情')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> &rarr;
    <a href="{{ url('my-goods') }}">我的商品</a> &rarr;
    商品详情
@stop
@section('right')
    <div class="row">
        <div class="col-sm-12 my-goods goods-detail">
            <div class="row operating">
                <div class="col-sm-1">
                    <a href="javascript:history.back()"><i class="fa fa-reply"></i> 返回</a>
                </div>
                <div class="col-sm-3 col-sm-push-8 text-right btn-list">
                    <a href="{{ url('my-goods/' . $goods->id . '/edit') }}" class="btn btn-success">编辑</a>
                    <a href="javascript:" data-method="put"
                       data-url="{{ url('api/v1/my-goods/shelve')}}"
                       data-status="{{ $goods->status }}"
                       data-data='{ "id": "{{ $goods->id }}" }'
                       data-on='上架'
                       data-off='下架'
                       class="ajax-no-form  shelve btn btn-info">
                        {{ cons()->valueLang('goods.status' , !$goods->status) }}
                    </a>

                    <a class="btn btn-remove delete no-form ajax" data-method="delete"
                       data-url="{{ url('api/v1/my-goods/' . $goods->id) }}"
                       data-done-url="{{ url('my-goods') }}">删除</a>
                </div>
            </div>

            <div class="row good-wrap">
                <div class="col-sm-3 commodity-img">
                    <img class="commodity" src="{{ $goods->image_url }}">
                </div>
                <div class="col-sm-9 detail-list-wrap">
                    <div class="store-name">
                        <span class="prompt">名称 :</span> <b>{{ $goods->name }}</b>
                    </div>
                    <ul class="left-list pull-left">
                        <li><span class="prompt">商品ID : </span>{{ $goods->id }}</li>
                        <li><span class="prompt">条形码 : </span>{{ $goods->bar_code }}</li>
                        @if(auth()->user()->type==cons('user.type.wholesaler'))
                            <li>
                                <span class="prompt">价格 :</span>
                                <b class="red">¥{{ $goods->price .' / ' . $goods->pieces  }}</b>
                            </li>
                        @else
                            <li>
                                <span class="prompt">价格(终端商) :</span>
                                <b class="red">¥{{ $goods->price .' / ' . $goods->pieces  }}</b>
                            </li>
                            <li>
                                <span class="prompt">价格(批发商) :</span>
                                <b class="red">¥{{ $goods->price_wholesaler .' / ' . cons()->valueLang('goods.pieces',$goods->pieces_wholesaler)  }}</b>
                            </li>
                        @endif
                        <li><span class="prompt">状态 :</span> <b
                                    class="red">已{{ cons()->valueLang('goods.status' ,$goods->status) }}</b></li>
                        @foreach($attrs as $key => $attr)
                            <li><span class="prompt">{{ $key }} :</span> <b>{{ $attr }}</b></li>
                        @endforeach
                    </ul>
                    <ul class="right-list">
                        <li><span class="prompt">保质期 :</span> <b>{{ $goods->shelf_life }}</b>
                        </li>
                        <li><span class="prompt">是否新品 :</span>
                            <b>{{ cons()->valueLang('goods.type' ,$goods->is_new ) }}</b>
                        </li>
                        <li>
                            <span class="prompt">是否缺货 :</span>
                            <b>{{ cons()->valueLang('goods.type' ,$goods->is_out) }}</b>
                        </li>
                        <li><span class="prompt">即期品 :</span>
                            <b>{{ cons()->valueLang('goods.type' ,$goods->is_expire ) }}</b></li>
                        @if( $goods->is_back || $goods->is_change)
                            <li><span class="prompt">退换货 :</span> <b>{{ $goods->is_back ? '可退货' : '' }}</b>
                                <b>{{  $goods->is_change ? '可换货' : ($goods->is_back ? '' : '不可退 不可换')  }}</b></li>
                        @endif
                        @if(auth()->user()->type==cons('user.type.wholesaler'))
                            <li>
                                <span class="prompt">规格 :</span> <b>{{ $goods->specification or '暂无' }}</b>
                            </li>
                        @else
                            <li>
                                <span class="prompt">规格(终端商) :</span> <b>{{ $goods->specification or '暂无' }}</b>
                            </li>
                            <li>
                                <span class="prompt">规格(批发商) :</span>
                                <b>{{ $goods->specification_wholesaler or '暂无' }}</b>
                            </li>
                        @endif

                        @if($goods->is_promotion)
                            <li class="clearfix">
                                <span class="prompt pull-left">促销信息 :&nbsp;</span>
                                <p class="promotions-content"> {{ $goods->promotion_info }}</p>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-sm-12">
                    <span class="fa fa-star-o"></span> 累计销售量 : <span>{{ $goods->sales_volume }}</span>
                </div>
            </div>

            <div class="row delivery-wrap">
                <div class="col-sm-12 switching">
                    <a href="javascript:void(0)" id="location" class="active">配送区域</a>
                    <a href="javascript:void(0)" id="graphic-details">图文详情</a>
                </div>
                <div class="col-sm-12 address-wrap location box">
                    <div class="item">
                        <h5 class="prompt">商品配送区域 :</h5>
                        <ul class="address-list">
                            @foreach($goods->deliveryArea as $area)
                                <li>{{ $area->address_name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    {{--<div class="item">--}}
                    {{--<h5 class="prompt">商品配送区域大概地图标识 :</h5>--}}

                    {{--<p class="address-map">--}}
                    {{--<img src="http://placehold.it/300x250/CDF" alt="" title=""/>--}}
                    {{--</p>--}}
                    {{--<div id="map"></div>--}}
                    {{--</div>--}}
                </div>
                <div class="col-sm-12 box graphic-details">
                    {!! $goods->introduce !!}
                </div>

            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            tabBox();
            myGoodsFunc();
        });
        {{--$(document).ready(function () {--}}
        {{--@if(isset($coordinates))--}}
        {{--getCoordinateMap({!! $coordinates !!});--}}
        {{--@endif--}}
        {{--});--}}
    </script>
@stop