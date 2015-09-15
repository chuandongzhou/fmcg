@extends('index.manage-left')

@section('subtitle', '商品详情')

@section('right')
    <div class="col-sm-10  goods-detail">
        <div class="row operating">
            <div class="col-sm-1">
                <a>< 返回</a>
            </div>
            <div class="col-sm-3 col-sm-push-8 text-right btn-list">
                <a href="{{ url('goods/' . $goods->id . '/edit') }}" class="btn btn-success">编辑</a>
                <a data-url="{{ url('api/v1/goods/shelve/' . $goods->id) }}" data-method="post"
                   data-data='{ "status": "{{ !$goods->status }}" }' class="btn btn-info no-form ajax"
                   data-done-then="refresh">{{ cons()->valueLang('goods.status' , !$goods->status) }}</a>
                <a class="btn btn-remove delete no-form ajax" data-method="delete"
                   data-url="{{ url('api/v1/goods/' . $goods->id) }}" data-done-url="{{ url('goods') }}">删除</a>
            </div>
        </div>

        <div class="row good-wrap">
            <div class="col-sm-3 commodity-img">
                <img class="commodity" src="{{ $goods->image_url }}">
            </div>
            <div class="col-sm-9 detail-list-wrap">
                <ul>
                    <li>名称 : {{ $goods->name }}</li>
                    <li>价格 : <span class="red">￥{{ $goods->price }}</span></li>
                </ul>
                <ul class="left-list pull-left">
                    <li>状态 : <span class="red">已{{ cons()->valueLang('goods.status' ,$goods->status) }}</span></li>
                    <li>品牌 : xxx牌</li>
                    <li>类别 : 饮料</li>
                    <li>包装 : 箱装</li>
                    <li>规格 : 350ml/瓶 , 共12瓶</li>
                </ul>
                <ul class="right-list ">
                    <li>是否新品 : {{ cons()->valueLang('goods.type' ,$goods->is_new ) }}</li>
                    <li>是否缺货 : {{ cons()->valueLang('goods.type' ,$goods->is_out) }}</li>
                    <li>即期品 : {{ cons()->valueLang('goods.type' ,$goods->is_expire ) }}</li>
                    @if( $goods->is_back || $goods->is_change)
                        <li>退换货 : {{ $goods->is_back ? '可退货' : '' }}  {{  $goods->is_change ? '可换货' : ''  }}</li>
                    @endif
                    @if($goods->is_promotion)
                        <li>促销信息 : {{ $goods->promotion_info }}</li>
                    @endif
                </ul>
            </div>
            <div class="col-sm-12">
                <span class="fa fa-star-o"></span> 累计销售量 : <span>{{ $goods->sales_volume }}</span>
            </div>
        </div>

        <div class="row delivery-wrap">
            <div class="col-sm-12 switching">
                <a id="location" class="active">配送区域</a>
                <a id="graphic-details">图文详情</a>
            </div>
            <div class="col-sm-12 location box">
                <p>商品配送区域 :</p>
                @foreach($goods->deliveryArea as $area)
                    <p class="col-sm-12">{{ $area->detail_address }}</p>
                @endforeach
                <p>商品配送区域大概地图标识 :</p>

                <p class="address-map">
                    <img src="http://placehold.it/300x250/CDF" alt="" title=""/>
                </p>
            </div>
            <div class="col-sm-12 box graphic-details">
                {!! $goods->introduce !!}
            </div>

        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            tabBox();
        })
    </script>
@stop